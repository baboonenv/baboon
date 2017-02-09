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
                noty({
                    type: 'success',
                    text: 'Theme Site successfully downloaded and enabled! -> ',
                    timeout: 2000
                });
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
        /**
         * Deploy To FTP system related functions
         */
        deployToFTP: function () {
            var themeDeployFTPIsConfigured = Routing.generate('bb_panel_deploy_ftp_is_configured');
            $.get(themeDeployFTPIsConfigured, function(data){
                if(data.isConfigured == false){
                    ConfigureTheme.configureFTP();
                    return;
                }
                var deployToFTPPath = Routing.generate('bb_panel_deploy_ftp');
                $.fancybox({
                    type: 'ajax',
                    href: deployToFTPPath
                });
            });
        },
        postDeployToFTP: function () {
            var postDeployFTP = Routing.generate('bb_panel_post_deploy_ftp');
            var passwordForm = $('form[name="ftp_configuration"]');
            $.post(postDeployFTP, passwordForm.serialize(),function(data){
                if(data.success == true){
                    noty({
                        type: 'success',
                        text: 'Theme Site successfully deployed to FTP! -> ',
                        timeout: 2000
                    });
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
        },
        /**
         * Deploy To Git system related functions
         */
        deployToGit: function () {
            var themeDeployGitIsConfigured = Routing.generate('bb_panel_deploy_git_is_configured');
            $.get(themeDeployGitIsConfigured, function(data){
                if(data.isConfigured == false){
                    ConfigureTheme.configureGit();
                    return;
                }
                ConfigureTheme.postDeployToGit();
            });
        },
        postDeployToGit: function () {
            var postDeployGit = Routing.generate('bb_panel_post_deploy_git');
            $.post(postDeployGit, function(data){
                if(data.success == true){
                    noty({
                        type: 'success',
                        text: 'Theme Site successfully deployed to Git!',
                        timeout: 3000
                    });
                }
            });
        },
        configureGit: function () {
            var GitConfigurationPath = Routing.generate('bb_panel_deploy_configure_git');
            $.fancybox({
                width: '600px',
                type: 'ajax',
                href: GitConfigurationPath
            });
        },
        updateConfigureGit: function () {
            var GitConfigurationForm = $('form[name="git_configuration"]');
            $.post( GitConfigurationForm.attr('action'), GitConfigurationForm.serialize(), function( data ) {
                $.fancybox(data);
            });
        },
        testGitConnection: function () {
            var GitConfigurationForm = $('form[name="git_configuration"]');
            var GitConnectionTestPath = Routing.generate('bb_panel_deploy_git_connection_test');
            $.post( GitConnectionTestPath, GitConfigurationForm.serialize(), function( data ) {
                $.fancybox(data);
            });
        },
        normalizeConfigureGitForm: function () {
            var deployType = $('#git_configuration_deployType').val();
            var emailWrap = $('#git_configuration_email').parent().parent();
            var passwordWrap = $('#git_configuration_password').parent().parent();
            if(deployType == 'ssh'){
                emailWrap.addClass('hidden');
                passwordWrap.addClass('hidden');
            }else if(deployType == 'https'){
                emailWrap.removeClass('hidden');
                passwordWrap.removeClass('hidden');
            }
        }
    };
});
