$(document).ready(function() {
    ConfigureAsset = {
        configureField: function ($this, $assetKey) {
            $.fancybox({
                type: 'ajax',
                href: Routing.generate('bb_panel_field_configure', {
                    assetKey: $assetKey
                }),
                autoSize: false,
                width: '600px',
                maxWidth: '600px',
                height: 'auto'
            });
        }
    };
});