<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAttributeController extends Controller
{
    /**
     * Display attributes and attribute groups list.
     */
    public function index(Request $request): View
    {
        $groups = AttributeGroup::withCount('attributes')->orderBy('sort_order')->orderBy('name')->get();
        
        $query = Attribute::with('group')->withCount('values');
        
        if ($request->filled('group_id')) {
            $query->where('attribute_group_id', $request->input('group_id'));
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $attributes = $query->orderBy('name')->get();
        $products = Product::select('id', 'name', 'sku')->orderBy('name')->get();

        return view('admin.products.attributes', compact('groups', 'attributes', 'products'));
    }

    /**
     * Store a newly created attribute group.
     */
    public function storeGroup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        AttributeGroup::create([
            'name'       => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute group created successfully.');
    }

    /**
     * Update an attribute group.
     */
    public function updateGroup(Request $request, AttributeGroup $group): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $group->update($validated);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute group updated successfully.');
    }

    /**
     * Delete an attribute group.
     */
    public function destroyGroup(AttributeGroup $group): RedirectResponse
    {
        $group->delete();

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute group deleted successfully.');
    }

    /**
     * Store a newly created attribute.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attribute_group_id' => 'nullable|exists:attribute_groups,id',
            'name'               => 'required|string|max:100',
            'code'               => 'nullable|string|max:100|unique:attributes,code',
            'type'               => 'required|in:text,select,multiselect,color,boolean',
            'options'            => 'nullable|string',
            'is_filterable'      => 'nullable|boolean',
            'is_comparable'      => 'nullable|boolean',
        ]);

        $code = !empty($validated['code']) 
            ? Str::slug($validated['code'], '_') 
            : Str::slug($validated['name'], '_');

        $baseCode = $code;
        $c = 1;
        while (Attribute::where('code', $code)->exists()) {
            $code = "{$baseCode}_{$c}";
            $c++;
        }

        // Process options string to array if provided
        $optionsArray = null;
        if (!empty($validated['options'])) {
            $optionsArray = array_values(array_filter(array_map('trim', explode(',', $validated['options']))));
        }

        Attribute::create([
            'attribute_group_id' => $validated['attribute_group_id'] ?? null,
            'name'               => $validated['name'],
            'code'               => $code,
            'type'               => $validated['type'],
            'options'            => $optionsArray,
            'is_filterable'      => $request->boolean('is_filterable', true),
            'is_comparable'      => $request->boolean('is_comparable', true),
        ]);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute specification created successfully.');
    }

    /**
     * Update an attribute.
     */
    public function update(Request $request, Attribute $attribute): RedirectResponse
    {
        $validated = $request->validate([
            'attribute_group_id' => 'nullable|exists:attribute_groups,id',
            'name'               => 'required|string|max:100',
            'code'               => 'required|string|max:100|unique:attributes,code,' . $attribute->id,
            'type'               => 'required|in:text,select,multiselect,color,boolean',
            'options'            => 'nullable|string',
            'is_filterable'      => 'nullable|boolean',
            'is_comparable'      => 'nullable|boolean',
        ]);

        $optionsArray = null;
        if (!empty($validated['options'])) {
            $optionsArray = array_values(array_filter(array_map('trim', explode(',', $validated['options']))));
        }

        $attribute->update([
            'attribute_group_id' => $validated['attribute_group_id'] ?? null,
            'name'               => $validated['name'],
            'code'               => Str::slug($validated['code'], '_'),
            'type'               => $validated['type'],
            'options'            => $optionsArray,
            'is_filterable'      => $request->boolean('is_filterable'),
            'is_comparable'      => $request->boolean('is_comparable'),
        ]);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute specification updated.');
    }

    /**
     * Delete an attribute.
     */
    public function destroy(Attribute $attribute): RedirectResponse
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute deleted.');
    }

    /**
     * Assign / update attribute values for a product.
     */
    public function saveProductAttributes(Request $request, Product $product): RedirectResponse
    {
        $values = $request->input('attributes', []);

        foreach ($values as $attributeId => $val) {
            $attribute = Attribute::find($attributeId);
            if (! $attribute) {
                continue;
            }

            if ($val === null || $val === '') {
                ProductAttributeValue::where('product_id', $product->id)
                    ->where('attribute_id', $attributeId)
                    ->delete();
                continue;
            }

            $record = ProductAttributeValue::firstOrNew([
                'product_id'   => $product->id,
                'attribute_id' => $attributeId,
            ]);

            if ($attribute->type === 'multiselect' && is_array($val)) {
                $record->value_json = $val;
                $record->value_text = implode(', ', $val);
            } else {
                $record->value_text = (string) $val;
                if (is_numeric($val)) {
                    $record->value_number = (float) $val;
                }
            }

            $record->save();
        }

        return back()->with('success', 'Product specifications updated successfully.');
    }
}

