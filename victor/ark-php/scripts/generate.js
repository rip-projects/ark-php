var fs = require('fs'),
    path = require('path'),
    mkdirp = require('mkdirp');

var appName = process.argv[2] || 'default';

console.log('generate', appName);

function copySync(srcFile, destFile) {
  if (fs.existsSync(destFile)) {
    var stat = fs.lstatSync(destFile);
    if (stat.isDirectory) {
      destFile = path.join(destFile, path.basename(srcFile));
    }
  }

  var BUF_LENGTH = 64 * 1024;
  var buff = new Buffer(BUF_LENGTH);
  var fdr = fs.openSync(srcFile, 'r');
  var fdw = fs.openSync(destFile, 'w');
  var bytesRead = 1;
  var pos = 0;
  while (bytesRead > 0) {
    bytesRead = fs.readSync(fdr, buff, 0, BUF_LENGTH, pos);
    fs.writeSync(fdw,buff,0,bytesRead);
    pos += bytesRead;
  }
  fs.closeSync(fdr);
  fs.closeSync(fdw);
}

var copy = function(from, to) {
  mkdirp.sync(to);
  var files = fs.readdirSync(from);
  for(var i in files) {
    if (files[i] !== '.git') {
      var f = path.join(from, files[i]);
      var fto = path.join(to, files[i]);
      var stat = fs.lstatSync(f);
      if (stat.isDirectory()) {
        copy(f, fto);
      } else {
        copySync(f, fto);
      }
    }
  }
};

var from = path.resolve('ark-php/skel');
var to = path.resolve(path.join('ark-php/application', appName));
copy(from, to);


