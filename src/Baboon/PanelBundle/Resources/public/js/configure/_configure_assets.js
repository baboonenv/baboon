$(document).ready(function() {
    ConfigureAsset = {
        configureField: function ($this, $assetPath) {
            $.fancybox({
                type: 'ajax',
                href: Routing.generate('bb_panel_field_configure', {
                    assetPath: $assetPath
                }),
                autoSize: false,
                width: '600px',
                maxWidth: '600px',
                height: 'auto',
                helpers: {
                    overlay: {
                        closeClick: false
                    }
                }
            });
        },
        saveAssetValue: function ($this, $assetPath) {
            var savePath = Routing.generate('bb_panel_field_save_asset_value');
            var value = $('#asset-data-wrap').val();
            $.post(savePath, {
                assetPath: $assetPath,
                value   : value
            }, function(data){
                if(data.success == true){
                    noty({
                        type: 'success',
                        text: 'Asset field value successfully updated!',
                        timeout: 2000
                    });
                    ConfigureAsset.loadAssetWrap($assetPath);
                    $.fancybox.close();
                }
            });
        },
        loadAssetWrap: function ($assetPath) {
            var wrapDiv = $('[data-path="'+$assetPath+'"]');
            var loadAssetPath = Routing.generate('bb_panel_get_asset_wrap', {
                'assetPath': $assetPath
            });
            $.get(loadAssetPath, function(data){
                wrapDiv.html(data);
            });
        },
        refreshImageValue: function () {
            var $imageVal = $('#upload_image_image').val();
            if($imageVal == ''){
                return;
            }
            $('#asset-data-wrap').val($imageVal);
        },
        refreshFileValue: function () {
            var $fileVal = $('#upload_file_file').val();
            if($fileVal == ''){
                return;
            }
            $('#asset-data-wrap').val($fileVal);
        }
    };
});