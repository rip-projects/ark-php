var _ = require('underscore'),
    semver = require('semver'),
    fs = require('fs'),
    path = require('path');


var package = (process.env.AM_PACKAGE) ? JSON.parse(process.env.AM_PACKAGE) : {};
var config = (process.env.AM_CONFIG) ? JSON.parse(process.env.AM_CONFIG) : {};

// console.log('package', package);
// console.log('config', config);

function doTemplate() {
  var core = '';
  for(var a in package.arkDependencies) {
    var b = a.split(':');
    b = path.join(config.cache, b[0].split('.').join('/'), b[1]);
    var c = fs.readdirSync(b);
    c = semver.maxSatisfying(c, package.arkDependencies[a]);
    core = path.join(b, c);
    break;
  }
  var t = fs.readFileSync('www/index.template.php', 'utf8');
  t = _.template(t, {
    core: core
  });
  fs.writeFileSync('www/index.php', t);
  fs.unlinkSync('www/index.template.php');
}

console.log('apply templates');
doTemplate();

