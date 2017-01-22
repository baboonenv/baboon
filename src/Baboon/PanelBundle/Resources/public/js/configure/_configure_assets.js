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
        },
        saveAssetValue: function ($this, $assetKey) {
            var savePath = Routing.generate('bb_panel_field_save_asset_value');
            var value = $('#asset-data-wrap').val();
            $.post(savePath, {
                assetKey: $assetKey,
                value   : value
            }, function(data){
                if(data.success == true){
                    noty({
                        type: 'success',
                        text: 'Asset field value successfully updated!'
                    });
                    $.fancybox.close();
                }
            });

        }
    };
});