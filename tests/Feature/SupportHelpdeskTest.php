<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\KbArticle;
use App\Models\KbCategory;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportHelpdeskTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'  => 'Head Concierge Admin',
            'email' => 'concierge.admin@nexon.com',
            'role'  => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'name'  => 'Ananya Singhania',
            'email' => 'ananya.s@example.com',
            'role'  => 'customer',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
    }

    // ==========================================
    // Job 26: Customer Support Tickets & SLA Desk
    // ==========================================

    public function test_support_tickets_index_renders_with_kpis_and_filters(): void
    {
        SupportTicket::create([
            'user_id'     => $this->customer->id,
            'subject'     => 'Silk Dupatta Color Matching Inquiry',
            'category'    => 'product_defect',
            'priority'    => 'medium',
            'status'      => 'open',
            'assigned_to' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.support.tickets.index'));

        $response->assertStatus(200);
        $response->assertSee('Customer Support &amp; Helpdesk Desk', false);
        $response->assertSee('Silk Dupatta Color Matching Inquiry');
        $response->assertSee('Ananya Singhania');
        $response->assertSee('Total Tickets');
    }

    public function test_admin_can_log_new_support_ticket_with_sla_due_dispatch(): void
    {
        $payload = [
            'user_id'     => $this->customer->id,
            'category'    => 'delivery_delay',
            'priority'    => 'urgent', // 4 hour SLA
            'subject'     => 'Urgent express delivery confirmation',
            'message'     => 'Customer needs delivery before tomorrow evening gala in South Mumbai.',
            'assigned_to' => $this->admin->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.support.tickets.store'), $payload);

        $this->assertDatabaseHas('support_tickets', [
            'user_id'  => $this->customer->id,
            'subject'  => 'Urgent express delivery confirmation',
            'priority' => 'urgent',
            'status'   => 'open',
        ]);

        $ticket = SupportTicket::where('subject', 'Urgent express delivery confirmation')->first();
        $this->assertNotNull($ticket);
        $this->assertStringStartsWith('TCK-', $ticket->ticket_number);

        // Verify SLA Due is roughly 4 hours ahead
        $this->assertNotNull($ticket->sla_due_at);
        $diffHours = Carbon::now()->diffInHours($ticket->sla_due_at);
        $this->assertGreaterThanOrEqual(3, $diffHours);
        $this->assertLessThanOrEqual(5, $diffHours);

        // Verify first message recorded
        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id'        => $ticket->id,
            'user_id'          => $this->admin->id,
            'is_internal_note' => false,
        ]);

        $response->assertRedirect(route('admin.support.tickets.show', $ticket));
    }

    public function test_admin_can_view_ticket_dossier_and_reply(): void
    {
        $ticket = SupportTicket::create([
            'user_id'     => $this->customer->id,
            'subject'     => 'Custom Hemming Length Request',
            'category'    => 'order_inquiry',
            'priority'    => 'high',
            'status'      => 'open',
            'assigned_to' => $this->admin->id,
        ]);

        SupportTicketMessage::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => $this->customer->id,
            'message'          => 'Please hem by 2 inches shorter.',
            'is_internal_note' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.support.tickets.show', $ticket));
        $response->assertStatus(200);
        $response->assertSee('Custom Hemming Length Request');
        $response->assertSee('Please hem by 2 inches shorter.');

        // Post public concierge reply
        $replyResponse = $this->actingAs($this->admin)->post(route('admin.support.tickets.reply', $ticket), [
            'message'          => 'Our master tailor has adjusted the hemming instructions for your atelier dispatch.',
            'is_internal_note' => 0,
            'change_status'    => 'in_progress',
        ]);

        $replyResponse->assertRedirect(route('admin.support.tickets.show', $ticket));

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id'        => $ticket->id,
            'message'          => 'Our master tailor has adjusted the hemming instructions for your atelier dispatch.',
            'is_internal_note' => false,
        ]);

        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
    }

    public function test_admin_can_post_private_internal_note(): void
    {
        $ticket = SupportTicket::create([
            'user_id'     => $this->customer->id,
            'subject'     => 'Discreet Delivery Request',
            'category'    => 'delivery_delay',
            'priority'    => 'low',
            'status'      => 'open',
            'assigned_to' => $this->admin->id,
        ]);

        $replyResponse = $this->actingAs($this->admin)->post(route('admin.support.tickets.reply', $ticket), [
            'message'          => 'Warehouse notified: pack in neutral matte black box with no exterior branding.',
            'is_internal_note' => 1,
        ]);

        $replyResponse->assertRedirect(route('admin.support.tickets.show', $ticket));

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id'        => $ticket->id,
            'is_internal_note' => true,
        ]);

        $viewResponse = $this->actingAs($this->admin)->get(route('admin.support.tickets.show', $ticket));
        $viewResponse->assertSee('Private Staff Note');
        $viewResponse->assertSee('Warehouse notified: pack in neutral matte black box');
    }

    public function test_admin_can_update_ticket_status_priority_and_assignment(): void
    {
        $ticket = SupportTicket::create([
            'user_id'     => $this->customer->id,
            'subject'     => 'Need Size Exchange Advice',
            'category'    => 'refund_return',
            'priority'    => 'medium',
            'status'      => 'open',
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.support.tickets.update-status', $ticket), [
            'status'      => 'resolved',
            'priority'    => 'high',
            'assigned_to' => $this->admin->id,
        ]);

        $response->assertRedirect(route('admin.support.tickets.show', $ticket));

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertEquals('high', $ticket->priority);
        $this->assertEquals($this->admin->id, $ticket->assigned_to);
        $this->assertNotNull($ticket->resolved_at);
    }

    // ==========================================
    // Job 27: Customer Knowledge Base
    // ==========================================

    public function test_knowledge_base_index_renders_with_articles_and_categories(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.support.kb.index'));

        $response->assertStatus(200);
        $response->assertSee('Knowledge Base Article Publisher');
        $response->assertSee('Customer Views');
        $response->assertSee('Live &amp; Published', false);
    }

    public function test_admin_can_create_kb_article_and_category(): void
    {
        // 1. Create Category
        $catResponse = $this->actingAs($this->admin)->post(route('admin.support.kb.category.store'), [
            'name'        => 'Atelier Silk Care',
            'icon'        => 'sparkles',
            'description' => 'Preserving delicate Mulberry silk garments.',
            'sort_order'  => 5,
        ]);

        $catResponse->assertRedirect(route('admin.support.kb.index'));
        $this->assertDatabaseHas('kb_categories', ['name' => 'Atelier Silk Care']);

        $category = KbCategory::where('name', 'Atelier Silk Care')->first();

        // 2. Create Article
        $articleResponse = $this->actingAs($this->admin)->post(route('admin.support.kb.store'), [
            'category_id'  => $category->id,
            'title'        => 'How to Cold Wash Mulberry Silk Kimonos',
            'slug'         => 'how-to-cold-wash-mulberry-silk',
            'summary'      => 'Essential laundering rules for maintaining fabric sheen.',
            'content'      => "1. Always hand wash in cold water with pH-neutral detergent.\n2. Never wring or twist.\n3. Flat dry in shade.",
            'is_published' => 1,
            'is_featured'  => 1,
        ]);

        $articleResponse->assertRedirect(route('admin.support.kb.index'));

        $this->assertDatabaseHas('kb_articles', [
            'title'        => 'How to Cold Wash Mulberry Silk Kimonos',
            'slug'         => 'how-to-cold-wash-mulberry-silk',
            'is_published' => true,
            'is_featured'  => true,
        ]);
    }

    public function test_admin_can_toggle_publish_status_and_update_article(): void
    {
        $category = KbCategory::firstOrCreate(
            ['slug' => 'sizing-assistance'],
            ['name' => 'Sizing Assistance', 'icon' => 'ruler', 'sort_order' => 1, 'is_active' => true]
        );

        $article = KbArticle::create([
            'category_id'  => $category->id,
            'title'        => 'Bra Band Measurement Guide',
            'slug'         => 'bra-band-measurement-guide-test',
            'summary'      => 'Step-by-step measurement guide.',
            'content'      => 'Measure directly under bust with measuring tape parallel to the floor.',
            'is_published' => true,
            'author_id'    => $this->admin->id,
        ]);

        // Toggle to draft
        $this->actingAs($this->admin)->post(route('admin.support.kb.toggle', $article));
        $article->refresh();
        $this->assertFalse($article->is_published);

        // Update content
        $this->actingAs($this->admin)->put(route('admin.support.kb.update', $article), [
            'category_id'  => $category->id,
            'title'        => 'Bra Band & Cup Measurement Master Guide',
            'slug'         => 'bra-band-measurement-guide-test',
            'summary'      => 'Updated measurement guide with cup chart.',
            'content'      => 'Measure underbust and bust apex.',
            'is_published' => 1,
        ]);

        $article->refresh();
        $this->assertEquals('Bra Band & Cup Measurement Master Guide', $article->title);
        $this->assertTrue($article->is_published);
    }

    public function test_storefront_help_center_and_article_reader_renders(): void
    {
        $category = KbCategory::firstOrCreate(
            ['slug' => 'dispatch-logistics'],
            ['name' => 'Dispatch & Logistics', 'icon' => 'truck', 'sort_order' => 2, 'is_active' => true]
        );

        $article = KbArticle::create([
            'category_id'  => $category->id,
            'title'        => 'Discreet Packaging Guarantee',
            'slug'         => 'discreet-packaging-guarantee',
            'summary'      => 'All items dispatched in unmarked plain exterior packaging.',
            'content'      => "We ensure 100% confidential shipping. The exterior package is a plain box with LS Logistics sender details.",
            'is_published' => true,
            'is_featured'  => true,
            'views_count'  => 10,
            'author_id'    => $this->admin->id,
        ]);

        // 1. Visit Help Center index
        $helpResponse = $this->get(route('help.index'));
        $helpResponse->assertStatus(200);
        $helpResponse->assertSee('Help Center');
        $helpResponse->assertSee('Discreet Packaging Guarantee');

        // 2. Search for article
        $searchResponse = $this->get(route('help.index', ['q' => 'Discreet']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Search Results for');
        $searchResponse->assertSee('Discreet Packaging Guarantee');

        // 3. Read specific article
        $articleResponse = $this->get(route('help.show', $article->slug));
        $articleResponse->assertStatus(200);
        $articleResponse->assertSee('Discreet Packaging Guarantee');
        $articleResponse->assertSee('plain box with LS Logistics sender details');

        // Verify view count incremented
        $article->refresh();
        $this->assertEquals(11, $article->views_count);
    }

    // ==========================================
    // Job 28: Storefront FAQ Builder
    // ==========================================

    public function test_faq_builder_index_renders_seeded_faqs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.support.faq.index'));

        $response->assertStatus(200);
        $response->assertSee('Storefront FAQ Builder');
        $response->assertSee('Total Questions');
        $response->assertSee('Live &amp; Active', false);
    }

    public function test_admin_can_create_update_and_toggle_faq(): void
    {
        // 1. Create FAQ
        $createResponse = $this->actingAs($this->admin)->post(route('admin.support.faq.store'), [
            'question'   => 'Do you ship to military APO/FPO addresses?',
            'answer'     => 'Yes, through certified speed post express couriers.',
            'category'   => 'Shipping & Delivery',
            'sort_order' => 10,
            'is_active'  => 1,
        ]);

        $createResponse->assertRedirect(route('admin.support.faq.index'));
        $this->assertDatabaseHas('faqs', [
            'question' => 'Do you ship to military APO/FPO addresses?',
            'category' => 'Shipping & Delivery',
        ]);

        $faq = Faq::where('question', 'Do you ship to military APO/FPO addresses?')->first();
        $this->assertNotNull($faq);

        // 2. Toggle active status
        $this->actingAs($this->admin)->post(route('admin.support.faq.toggle', $faq));
        $faq->refresh();
        $this->assertFalse($faq->is_active);

        // 3. Update FAQ
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.support.faq.update', $faq), [
            'question'   => 'Do you ship to international military addresses?',
            'answer'     => 'Yes, we provide international insured registered dispatch.',
            'category'   => 'Shipping & Delivery',
            'sort_order' => 12,
            'is_active'  => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.support.faq.index'));
        $faq->refresh();
        $this->assertEquals('Do you ship to international military addresses?', $faq->question);
        $this->assertTrue($faq->is_active);

        // 4. Delete FAQ
        $delResponse = $this->actingAs($this->admin)->delete(route('admin.support.faq.destroy', $faq));
        $delResponse->assertRedirect(route('admin.support.faq.index'));
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }

    public function test_storefront_faq_page_renders_active_faqs(): void
    {
        Faq::create([
            'question'   => 'How do I care for bespoke French Chantilly lace?',
            'answer'     => 'Hand wash with mild silk detergent at room temperature and dry flat away from direct sunlight.',
            'category'   => 'Lace Care & Sizing',
            'sort_order' => 1,
            'is_active'  => true,
        ]);

        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('How do I care for bespoke French Chantilly lace?');
        $response->assertSee('Lace Care &amp; Sizing', false);
    }
}
