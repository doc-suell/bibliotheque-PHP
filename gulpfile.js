"use strict"; // oblige à déclarer les variables ou constantes (let, var, const)

var browsersync = require("browser-sync"),
  gulp = require("gulp"),
  concat = require("gulp-concat"),
  notify = require("gulp-notify"),
  wait = require("gulp-wait"),
  postcss = require("gulp-postcss"),
  rename = require("gulp-rename"),
  sass = require("gulp-sass")(require("node-sass")),
  sourcemaps = require("gulp-sourcemaps"),
  uglify = require("gulp-uglify"),
  babel = require("gulp-babel");
var plugins = [
  require("autoprefixer")({ overrideBrowserslist: ["last 15 versions"] }),
  require("mqpacker")({
    sort: require("sort-css-media-queries"),
  }),
  require("cssnano")({ preset: ["default", { cssDeclarationSorter: false }] }),
];
gulp.task("browser-sync", function () {
  browsersync.init({
    // server: {
    // 	baseDir: "./" // à utiliser en cas de site statique (html)
    // }
    proxy: "http://bibliotheque/", // au cas où vous êtes sur un serveur local type wamp, mamp ou xamp
  });
});
// gulp.task('bs', function() {
// 	browsersync.init({
// 		// server: {
// 		// 	baseDir: "./" // à utiliser en cas de site statique (html)
// 		// }
// 		proxy: {
// 		    target: "https://preprod.mentorabyfrenchtooth.fr",
// 		},
// 		port: 8080,
// 		proxyRes: [
// 	        function(proxyRes, req, res) {
// 	            console.log(proxyRes.headers);
// 	        }
// 	    ]
// 		// proxy: "localhost/file_name/", au cas où vous êtes sur un serveur local type wamp, mamp ou xamp
// 	});
// });
gulp.task("style", function () {
  return (
    gulp
      .src("./assets/css/src/**/*.scss") //  on lui indique où sont les fichiers scss
      .pipe(wait(1000))
      .pipe(sourcemaps.init({ largeFile: true })) // va indexer tous les fichiers css
      .pipe(sass({ outputStyle: "compressed" }).on("error", notify.onError())) // on l'envoie au préprocesseur sass de façon compressé
      .pipe(rename({ suffix: ".min", prefix: "" })) // ajoute un suffixe au fichier css pour devenir style.min.css
      // .pipe(autoprefixer({overrideBrowserslist: ['last 15 versions']})) // autoprefixer automatiquement les propriétés css
      .pipe(postcss(plugins))
      .pipe(sourcemaps.write(".")) // ecrit le sourcemaps dans le fichier css compilé
      .pipe(gulp.dest("./assets/css/dist")) // on lui indique où il envoie le fichier compilé css
      .pipe(
        notify({ message: "Super, le CSS est bien compilé !!!", onLast: true })
      ) // afficher message succes si tout va bien
      .pipe(browsersync.stream())
  ); // rafraichi le navigateur avec les modifs
});

gulp.task("scripts", function () {
  return gulp
    .src([
      "./assets/libs/jquery/jquery-3.6.0.min.js",
      "./assets/js/src/**/*.js",
    ])
    .pipe(sourcemaps.init())
    .pipe(
      babel({
        presets: ["@babel/preset-env"],
      })
    )
    .pipe(concat("scripts.min.js"))
    .pipe(uglify())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest("./assets/js/dist"))
    .pipe(
      notify({ message: "Super, le JS est bien compilé !!!", onLast: true })
    ); // afficher message succes si tout va bien
  //.pipe(browsersync.reload({stream: true})); // rafraichi le navigateur avec les modifs
});

gulp.task("watch", function () {
  gulp.watch("./assets/css/src/**/*.scss", gulp.parallel("style"));
  gulp.watch("./assets/js/src/**/*.js", gulp.parallel("scripts"));
});

gulp.task(
  "default",
  gulp.parallel("style", "scripts", "watch", "browser-sync")
); // add bs for browsersyncr
