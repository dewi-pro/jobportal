<ul
    class="list-unstyled list-feature"
    id="auto-checkboxes"
    data-name="foo"
>
    <li
        id="mainNode"
        class="permissions-tree border-0"
        style="background-color: inherit;"
    >
        <x-core::form.checkbox
            label="{{ trans('core/acl::permissions.all') }}"
            id="expandCollapseAllTree"
            class="label label-default allTree"
        >
            <x-slot:label>
                <x-core::badge
                    color="secondary"
                    :label="trans('core/acl::permissions.all')"
                />
            </x-slot:label>
        </x-core::form.checkbox>
        <ul class="p-0 list-unstyled">
            @foreach ($children['root'] as $elementKey => $element)
                <li
                    class="collapsed mx-0"
                    style="background-color: inherit"
                    id="node{{ $elementKey }}"
                >
                    <x-core::form.checkbox
                        id="checkSelect{{ $elementKey }}"
                        name="flags[]"
                        value="{{ $flags[$element]['flag'] }}"
                        checked="{{ in_array($flags[$element]['flag'], $active) }}"
                    >
                        <x-slot:label>
                            <x-core::badge
                                lite
                                color="primary"
                                :label="$flags[$element]['name']"
                            />
                        </x-slot:label>
                    </x-core::form.checkbox>
                    @if (isset($children[$element]))
                        <ul class="list-unstyled">
                            @foreach ($children[$element] as $subKey => $subElements)
                                <li
                                    class="collapsed mx-0"
                                    style="background-color: inherit"
                                    id="node_sub_{{ $elementKey }}_{{ $subKey }}"
                                >
                                    <x-core::form.checkbox
                                        id="checkSelect_sub_{{ $elementKey }}_{{ $subKey }}"
                                        name="flags[]"
                                        value="{{ $flags[$subElements]['flag'] }}"
                                        checked="{{ in_array($flags[$subElements]['flag'], $active) }}"
                                    >
                                        <x-slot:label>
                                            <x-core::badge
                                                lite
                                                color="yellow"
                                                :label="$flags[$subElements]['name']"
                                            />
                                        </x-slot:label>
                                    </x-core::form.checkbox>
                                    @if (isset($children[$subElements]))
                                        <ul class="list-unstyled">
                                            @foreach ($children[$subElements] as $subSubKey => $subSubElements)
                                                <li
                                                    class="collapsed mx-0"
                                                    style="background-color: inherit"
                                                    id="node_sub_sub_{{ $subSubKey }}"
                                                >
                                                    <x-core::form.checkbox
                                                        id="checkSelect_sub_sub{{ $subSubKey }}"
                                                        name="flags[]"
                                                        value="{{ $flags[$subSubElements]['flag'] }}"
                                                        checked="{{ in_array($flags[$subSubElements]['flag'], $active) }}"
                                                    >
                                                        <x-slot:label>
                                                            <x-core::badge
                                                                lite
                                                                color="cyan"
                                                                :label="$flags[$subSubElements]['name']"
                                                            />
                                                        </x-slot:label>
                                                    </x-core::form.checkbox>
                                                    @if (isset($children[$subSubElements]))
                                                        <ul class="list-unstyled">
                                                            @foreach ($children[$subSubElements] as $grandChildrenKey => $grandChildrenElements)
                                                                <li
                                                                    class="collapsed mx-0"
                                                                    style="background-color: inherit"
                                                                    id="node_grand_child{{ $grandChildrenKey }}"
                                                                >
                                                                    <x-core::form.checkbox
                                                                        id="checkSelect_grand_child{{ $grandChildrenKey }}"
                                                                        name="flags[]"
                                                                        value="{{ $flags[$grandChildrenElements]['flag'] }}"
                                                                        checked="{{ in_array($flags[$grandChildrenElements]['flag'], $active) }}"
                                                                    >
                                                                        <x-slot:label>
                                                                            <x-core::badge
                                                                                lite
                                                                                color="lime"
                                                                                :label="$flags[
                                                                                    $grandChildrenElements
                                                                                ]['name']"
                                                                            />
                                                                        </x-slot:label>
                                                                    </x-core::form.checkbox>
                                                                    @if (isset($children[$grandChildrenElements]))
                                                                        <ul class="list-unstyled">
                                                                            @foreach ($children[$grandChildrenElements] as $grandChildrenKeySub => $greatGrandChildrenElements)
                                                                                <li
                                                                                    class="collapsed mx-0"
                                                                                    style="background-color: inherit"
                                                                                    id="node{{ $grandChildrenKey }}"
                                                                                >
                                                                                    <x-core::form.checkbox
                                                                                        label="{{ $flags[$grandChildrenElements]['name'] }}"
                                                                                        id="checkSelect_grand_child{{ $grandChildrenKeySub }}"
                                                                                        name="flags[]"
                                                                                        value="{{ $flags[$grandChildrenElements]['flag'] }}"
                                                                                        checked="{{ in_array($flags[$grandChildrenElements]['flag'], $active) }}"
                                                                                    >
                                                                                        <x-slot:label>
                                                                                            <x-core::badge
                                                                                                lite
                                                                                                color="purple"
                                                                                                :label="$flags[
                                                                                                    $grandChildrenElements
                                                                                                ]['name']"
                                                                                            />
                                                                                        </x-slot:label>
                                                                                    </x-core::form.checkbox>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </li>
</ul>
