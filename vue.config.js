module.exports = {
    publicPath: process.env.NODE_ENV === 'production'
        ? '/frontend/web/'
        : '/frontend/web/',
    //Output directory at build time
    outputDir: 'frontend/public',
    //Set directory for static resources
    assetsDir: 'assets/',
    //Output path of html
    //indexPath: 'index.html',
    //File name hash
    filenameHashing: false
    // The configuration is higher than that of css loader in chainWebpack
    // css: {
    //     loaderOptions: {
    //         css: {
    //             // options here will be passed to css-loader
    //         },
    //         postcss: {
    //             // options here will be passed to postcss-loader
    //         }
    //     }
    // }
}