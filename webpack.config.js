var Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('app', './assets/js/app.js')

    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // .addEntry('login', './assets/js/login.js')
    // .addEntry('dashboard', './assets/js/dashboard.js')
    // .addEntry('add-alias', './assets/js/add-alias.js')
    // .addEntry('edit-alias', './assets/js/edit-alias.js')
    // .addEntry('export-selection', './assets/js/export-selection.js')

    .copyFiles({
        from: './assets/images',
    })

;

module.exports = Encore.getWebpackConfig();
