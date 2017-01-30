$(document).ready(function() {
    ConfigureTheme = {
        loadServerCategories: function($this) {
            $this = $($this);
            var $url = $this.find(':selected').data('categories-href');
            if($url == ''){
                return;
            }
            $.get($url, function(data){
                $('#categories-box').html(data);
            });
        },
        loadCategoryThemes: function($this) {
            $this = $($this);
            var $themesUrl = $this.data('action-url');
            var $url = $this.find(':selected').data('themes-href');
            if($url == ''){
                return;
            }
            $.post($themesUrl, {url: $url} ,function(data){
                $('#themes-box').html(data);
            });
        },
        enableTheme: function($this) {
            $this = $($this);
            var $actionUrl = $this.parent().parent().data('enable-action');
            var $zipUrl = $this.data('zip-url');
            if($zipUrl == ''){
                return;
            }
            $.post($actionUrl, {zip: $zipUrl} ,function(data){
                alert(data);
            });
        },
        syncTheme: function ($this) {
            var themeDeploySyncPath = Routing.generate('bb_panel_sync_theme');
            $.get(themeDeploySyncPath, function(data){
                noty({
                    type: 'success',
                    text: 'Theme Site successfully synchronize! -> '+data,
                    timeout: 2000
                });
            });
        },
        deployToGit: function ($this) {
            alert('deploy to git');
        },
        deployToFTP: function ($this) {
            var themeDeployFTPIsConfigured = Routing.generate('bb_panel_deploy_ftp_is_configured');
            $.get(themeDeployFTPIsConfigured, function(data){
                if(data.isConfigured == false){
                    ConfigureTheme.configureFTP();
                }else{

                }
            });
        },
        configureFTP: function () {
            var FTPConfigurationPath = Routing.generate('bb_panel_deploy_configure_ftp');
            $.fancybox({
                type: 'ajax',
                href: FTPConfigurationPath
            });
        },
        updateConfigureFTP: function () {
            var FTPConfigurationForm = $('form[name="ftp_configuration"]');
            $.post( FTPConfigurationForm.attr('action'), FTPConfigurationForm.serialize(), function( data ) {
                $.fancybox(data);
            });
        },
        testFTPConnection: function () {
            var FTPConfigurationForm = $('form[name="ftp_configuration"]');
            var FTPConnectionTestPath = Routing.generate('bb_panel_deploy_ftp_connection_test');
            $.post( FTPConnectionTestPath, FTPConfigurationForm.serialize(), function( data ) {
                $.fancybox(data);
            });
        }
    };
});
