const mix = require('laravel-mix');
const lodash = require("lodash");
const folder = {
    src: "resources/", // source files
    dist: "public/", // build files
    dist_assets: "public/assets/" //build assets files
};

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

var third_party_assets = {
        css_js: [
                {"name": "jquery", "assets": ["./node_modules/jquery/dist/jquery.min.js"]},
                {"name": "slimscroll", "assets": ["./node_modules/slimscroll/example/ssmaster/jquery.slimscroll.min.js",]},
                {"name": "moment", "assets": ["./node_modules/moment/min/moment.min.js"]},
                {"name": "jquery-ui", "assets": ["./node_modules/jquery-ui-dist/jquery-ui.min.js"]},
                {"name": "line-awesome", "assets": ["./node_modules/line-awesome/dist/line-awesome/css/line-awesome.min.css"]},
                {"name": "select2", "assets": ["./node_modules/select2/dist/js/select2.min.js", "./node_modules/select2/dist/css/select2.min.css"]},    
                {"name": "fullcalendar", "assets": ["./node_modules/fullcalendar/dist/fullcalendar.min.js", "./node_modules/fullcalendar/dist/fullcalendar.min.css"]},
               
             ]
    };

	//copying third party assets
    lodash(third_party_assets).forEach(function (assets, type) {
        if (type == "css_js") {
            lodash(assets).forEach(function (plugin) {
                var name = plugin['name'],
                    assetlist = plugin['assets'],
                    css = [],
                    js = [];
                lodash(assetlist).forEach(function (asset) {
                    var ass = asset.split(',');
					for (let i = 0; i < ass.length; ++i) {
                    	if(ass[i].substr(ass[i].length - 3)  == ".js") {
                    		js.push(ass[i]);
                    	} else {
                    		css.push(ass[i]);
                    	}
                	};
                });
            	if(js.length > 0){
            		mix.combine(js, folder.dist_assets + "/libs/" + name + "/" + name + ".min.js");
            	}
            	if(css.length > 0){
            		mix.combine(css, folder.dist_assets + "/libs/" + name + "/" + name + ".min.css");
            	}
            });
        }
    });

    // mix.copyDirectory("./node_modules", folder.dist_assets + "/libs/tinymce");


     // copy all fonts
     var out = folder.dist_assets + "fonts";
     mix.copyDirectory(folder.src + "fonts", out);

    //copying all images
    var out = folder.dist_assets + "img";
    mix.copyDirectory(folder.src + "img", out);
	


    mix.css('resources/css/app.css', folder.dist_assets + "css").minify(folder.dist_assets + "css/app.css");
    mix.copy('node_modules/bootstrap/dist/css', 'public/assets/libs/bootstrap/css');
    mix.copy('node_modules/bootstrap/dist/js', 'public/assets/libs/bootstrap/js');
	mix.copy('resources/css/font-awesome.min.css', 'public/assets/css/font-awesome.min.css');
    mix.copy('node_modules/summernote/dist', 'public/assets/libs/summernote');
    mix.copy('node_modules/jquery.maskedinput/src/jquery.maskedinput.js', 'public/assets/libs/mask');
    mix.copy('resources/js/jquery.fullcalendar.js', 'public/assets/libs/fullcalendar/jquery.fullcalendar.js');
	mix.copy('node_modules/morris.js/morris.css', 'public/assets/libs/morris');
    mix.copy('node_modules/morris.js/morris.js', 'public/assets/libs/morris');
    mix.copy('node_modules/morris.js/morris.min.js', 'public/assets/libs/morris');
	mix.copy('node_modules/raphael/raphael.min.js', 'public/assets/libs/raphael');
	mix.copy('node_modules/sticky-kit/dist/sticky-kit.min.js', 'public/assets/libs/sticky-kit');
    mix.copy('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css', 'public/assets/css');
    mix.copy('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js', 'public/assets/js');
    mix.copy('node_modules/datatables.net/js/jquery.dataTables.min.js', 'public/assets/js');
    mix.copy('node_modules/chart.js/dist', 'public/assets/libs/chart.js');
	mix.copy('node_modules/multiselect-two-sides/dist/js/multiselect.min.js', 'public/assets/js/multiselect.min.js');
	mix.copy('node_modules/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js', 'public/assets/js');
	mix.copy('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css', 'public/assets/libs/bootstrap-tagsinput');
    mix.copy('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.js', 'public/assets/libs/bootstrap-tagsinput');
    mix.copy('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js', 'public/assets/libs/bootstrap-tagsinput');
    mix.copy('node_modules/line-awesome/dist/line-awesome/fonts', 'public/assets/libs/fonts');
  

    
    //copying demo pages related assets
    var app_pages_assets = {
        js: [
            folder.src + "js/pages/slimscroll.init.js",
            folder.src + "js/pages/task.init.js",
            folder.src + "js/pages/line-chart.init.js",
            folder.src + "js/pages/select2.init.js",
			folder.src + "js/pages/chart.init.js",
            folder.src + "js/pages/summernote.init.js",
            folder.src + "js/pages/datetimepicker.init.js",
            folder.src + "js/pages/datatable.init.js",
            folder.src + "js/pages/mask.init.js",
            folder.src + "js/pages/form-validation.init.js",
			folder.src + "js/pages/dropfiles.init.js",
            
        ]
    };

    var out = folder.dist_assets + "js/";
    lodash(app_pages_assets).forEach(function (assets, type) {
		for (let i = 0; i < assets.length; ++i) {
        	mix.js(assets[i], out + "pages");
    	};
    });

	
    mix.combine('resources/js/app.js', folder.dist_assets + "js/app.min.js");
    mix.combine('resources/css/bootstrap-datetimepicker.min.css', folder.dist_assets + "css/bootstrap-datetimepicker.min.css");
    mix.combine('resources/js/bootstrap-datetimepicker.min.js', folder.dist_assets + "js/bootstrap-datetimepicker.min.js");
   
	