{{-- Navbar notification --}}

<li class="{{ $makeListItemClass() }}" id="{{ $id }}">

    {{-- Link --}}
    <a @if($enableDropdownMode) href="" @endif {{ $attributes->merge($makeAnchorDefaultAttrs()) }}>

        {{-- Icon --}}
        <i class="{{ $makeIconClass() }}"></i>

        {{-- Badge --}}
        <span class="{{ $makeBadgeClass() }}">{{ $badgeLabel }}</span>

    </a>

    {{-- Dropdown Menu --}}
    @if($enableDropdownMode)

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

            {{-- Custom dropdown content provided by external source --}}
            <div class="adminlte-dropdown-content"></div>

            {{-- Dropdown divider --}}
            <div class="dropdown-divider"></div>

            {{-- Dropdown footer with POST form --}}
            <form method="POST" action="{{ $attributes->get('href') }}">
                @csrf
                <button type="submit" class="dropdown-item dropdown-footer">
                    @isset($dropdownFooterLabel)
                        {{ $dropdownFooterLabel }}
                    @else
                        <i class="fas fa-lg fa-search-plus"></i>
                    @endisset
                </button>
            </form>

        </div>

    @endif

</li>

{{-- If required, update the notification periodically --}}

@if (! is_null($makeUpdateUrl()) && $makeUpdatePeriod() > 0)
@push('js')
<script>

    $(() => {
        let updateNotification = (nLink) =>
        {
            $.ajax({
                url: "{{ $makeUpdateUrl() }}"
            })
            .done((data) => {
                nLink.update(data);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
            });
        };

        let nLink = new _AdminLTE_NavbarNotification("{{ $id }}");
        updateNotification(nLink);

        setInterval(updateNotification, {{ $makeUpdatePeriod() }}, nLink);
    })

</script>
@endpush
@endif

@once
@push('js')
<script>

    class _AdminLTE_NavbarNotification {
        constructor(target)
        {
            this.target = target;
        }

        update(data)
        {
            let t = $(`li#${this.target}`);

            if (t.length <= 0 || ! data) {
                return;
            }

            let badge = t.find(".navbar-badge");
            let icon = t.find(".nav-link > i");
            let dropdown = t.find(".adminlte-dropdown-content");

            if (data.label && data.label > 0) {
                badge.html(data.label);
            } else {
                badge.empty();
            }

            if (data.label_color) {
                badge.removeClass((idx, classes) => {
                    return (classes.match(/(^|\s)badge-\S+/g) || []).join(' ');
                }).addClass(`badge-${data.label_color} badge-pill`);
            }

            if (data.icon_color) {
                icon.removeClass((idx, classes) => {
                    return (classes.match(/(^|\s)text-\S+/g) || []).join(' ');
                }).addClass(`text-${data.icon_color}`);
            }

            if (data.dropdown && dropdown.length > 0) {
                dropdown.html(data.dropdown);
            }
        }
    }

</script>
@endpush
@endonce
