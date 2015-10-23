/*! serializeObject - v1.0.0 - 2012-11-12
* https://github.com/danheberden/serializeObject
* Copyright (c) 2012 Dan Heberden; Licensed MIT, GPL */

(function( $ ){
  $.fn.serializeObject = function() {

    // don't do anything if we didn't get any elements
    if ( this.length < 1) {
      return false;
    }

    var data = {};
    var lookup = data; //current reference of data
    var selector = ':input[type!="checkbox"][type!="radio"], input:checked';
    var parse = function() {

      // data[a][b] becomes [ data, a, b ]
      var named = this.name.replace(/\[([^\]]+)?\]/g, ',$1').split(',');
      var cap = named.length - 1;
      var $el = $( this );

      // Ensure that only elements with valid `name` properties will be serialized
      if ( named[ 0 ] ) {
        for ( var i = 0; i < cap; i++ ) {
          // move down the tree - create objects or array if necessary
          lookup = lookup[ named[i] ] = lookup[ named[i] ] ||
            ( named[ i + 1 ] === "" ? [] : {} );
        }

        // at the end, push or assign the value
        if ( lookup.length !==  undefined ) {
          lookup.push( $el.val() );
        }else {
          lookup[ named[ cap ] ]  = $el.val();
        }

        // assign the reference back to root
        lookup = data;
      }
    };

    // first, check for elements passed into this function
    this.filter( selector ).each( parse );

    // then parse possible child elements
    this.find( selector ).each( parse );

    // return data
    return data;
  };
}( jQuery ));

var dateFormat = function () {
    var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
    timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[\-+]\d{4})?)\b/g,
    timezoneClip = /[^-+\dA-Z]/g,
    pad = function (val, len) {
        val = String(val);
        len = len || 2;
        while (val.length < len) val = "0" + val;
        return val;
    };

    // Regexes and supporting functions are cached through closure
    return function (date, mask, utc) {
        var dF = dateFormat;

        // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
        if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
            mask = date;
            date = undefined;
        }

        // Passing date through Date applies Date.parse, if necessary
        date = date ? new Date(date) : new Date();
        if (isNaN(date)) throw SyntaxError("invalid date");

        mask = String(dF.masks[mask] || mask || dF.masks["default"]);

        // Allow setting the utc argument via the mask
        if (mask.slice(0, 4) == "UTC:") {
            mask = mask.slice(4);
            utc = true;
        }

        var _ = utc ? "getUTC" : "get",
        d = date[_ + "Date"](),
        D = date[_ + "Day"](),
        m = date[_ + "Month"](),
        y = date[_ + "FullYear"](),
        H = date[_ + "Hours"](),
        M = date[_ + "Minutes"](),
        s = date[_ + "Seconds"](),
        L = date[_ + "Milliseconds"](),
        o = utc ? 0 : date.getTimezoneOffset(),
        flags = {
            d:    d,
            dd:   pad(d),
            ddd:  dF.i18n.dayNames[D],
            dddd: dF.i18n.dayNames[D + 7],
            m:    m + 1,
            mm:   pad(m + 1),
            mmm:  dF.i18n.monthNames[m],
            mmmm: dF.i18n.monthNames[m + 12],
            yy:   String(y).slice(2),
            yyyy: y,
            h:    H % 12 || 12,
            hh:   pad(H % 12 || 12),
            H:    H,
            HH:   pad(H),
            M:    M,
            MM:   pad(M),
            s:    s,
            ss:   pad(s),
            l:    pad(L, 3),
            L:    pad(L > 99 ? Math.round(L / 10) : L),
            t:    H < 12 ? "a"  : "p",
            tt:   H < 12 ? "am" : "pm",
            T:    H < 12 ? "A"  : "P",
            TT:   H < 12 ? "AM" : "PM",
            Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
            o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
            S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
        };

        return mask.replace(token, function ($0) {
            return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
        });
    };
}();

// Some common format strings
dateFormat.masks = {
    "default":      "dd/mm/yyyy HH:MM:ss",
    shortDate:      "m/d/yy",
    mediumDate:     "mmm d, yyyy",
    longDate:       "mmmm d, yyyy",
    fullDate:       "dddd, mmmm d, yyyy",
    shortTime:      "h:MM TT",
    mediumTime:     "h:MM:ss TT",
    longTime:       "h:MM:ss TT Z",
    isoDate:        "yyyy-mm-dd",
    isoTime:        "HH:MM:ss",
    isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
    isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'",
    xinixDate:      "dd/mm/yyyy",
    xinixTime:      "HH:MM:ss"
};

// Internationalization strings
dateFormat.i18n = {
    dayNames: [
    "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
    "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
    ],
    monthNames: [
    "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
    "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
    ]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
    return dateFormat(this, mask, utc);
};


/******************************************************************************/

var xn = window.xn = (function() {
    var xn = {
        config: {
            baseUrl: $('script[data-base-url]').attr('data-base-url') || ''
        },
        helper: {
            setCookie: function(c_name,value,exdays) {
                var exdate=new Date();
                exdate.setDate(exdate.getDate() + exdays);
                var c_value = escape(value) + ((exdays === null) ? "" : "; expires="+exdate.toUTCString());
                document.cookie=c_name + "=" + c_value;
            },

            getCookie: function(c_name) {
                var i,x,y,ARRcookies=document.cookie.split(";");
                for (i=0;i<ARRcookies.length;i++) {
                    x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
                    y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
                    x=x.replace(/^\s+|\s+$/g,"");
                    if (x==c_name) {
                        return unescape(y);
                    }
                }
            },

            siteUrl: function(uri) {
                return xn.config.baseUrl + uri;
            },

            segments: function(url) {
                var uri = url.replace(xn.config.baseUrl, '');
                return ('/' + uri).split('/');
            },

            stylize: function(selector) {
                selector = selector || 'body';

                $(selector).find('input[type=submit], input[type=button]').not('.btn').addClass('btn');

                // $(selector).find('li.has-children > a').filter(function() {
                //     return $(this).find('.caret').length === 0;
                // }).append('<span class="caret"></span>').addClass('dropdown');

                $(selector).find('.table thead tr:not(.btn-inverse)').addClass('btn-inverse');

                $(selector).find('.table thead tr a.asc').prepend('<i class="icon-white icon-chevron-up"></i>');
                $(selector).find('.table thead tr a.desc').prepend('<i class="icon-white icon-chevron-down"></i>');

                $(selector).find('.highlight-phrase').addClass('label label-warning');

                $(selector).find('.breadcrumb li:not(:last)').append('<span class="divider">&gt;</span>');

                $(selector).find('.submenu span').each(function() {
                    $(this).find('a').addClass('icon-' + $(this).attr('class'));
                });

                $(selector).find('a[data-class=home]').each(function() {
                    $(this).html('<span class="icon-home" style="text-indent: 10000px; overflow: hidden">' + $(this).html() + '</span>');
                });
                $(selector).find('.breadcrumb li:first a').each(function() {
                    $(this).addClass('icon-home');
                    $(this).css({
                        'text-indent': 100000,
                        'overflow': 'hidden'
                    });
                });

                $(selector).find('form').each(function() {
                    $(this).find('input[type=submit]:first').addClass('btn-primary');
                });

                $(selector).find('.mandatory').append('<i style="color: red">*</i>');

                $(selector).find('a.mass-action').each(function() {
                    $(this).attr('data-href', $(this).attr('href'));
                }).click(function(evt) {
                    var href = $(this).attr('data-href');

                    var selectedList = [];
                    $('table.grid').find('tr.grid_row *:checked').parents('tr').each(function(index, node) {
                        if (selectedList[0] != $(node).attr('data-ref')) {
                            selectedList.push($(node).attr('data-ref'));
                        }
                    });
                    $(this).attr('data-selected', selectedList.join(','));
                    $(this).attr('href', href + '/' + selectedList.join(','));
                    if (selectedList.join(',') === '') {
                        xn.helper.alert(_.template($('#template-no-data-selected-error').html()));
                        evt.preventDefault();
                        return false;
                    }

                    return true;
                });

                $(selector).find('input[type=checkbox].track-value').each(function() {
                    if (!$('input[name=' + $(this).attr('name') + '][type=hidden]').length) {
                        $(this).before('<input type="hidden" name="' + $(this).attr('name') + '" value="" />');
                    }
                });

                $(selector).find('fieldset > div').each(function() {
                    if (!$(this).hasClass('row-fluid')) {
                        $(this).addClass('row-fluid');
                        var children = $(this).children();
                        if (children.length >= 2 && children[0].tagName == 'LABEL') {
                            $(children[0]).addClass('span2');
                            $(children[1]).addClass('span10');
                            if (children.length > 2) {
                                children = children.splice(1);
                                var $div = $('<div class="span10" />');
                                $(children[0]).after($div);
                                for(var i in children) {
                                    $(children[i]).appendTo($div);
                                }
                            }
                        }
                    }
                });

                xn.helper.alert();
            },

            alert: function(msg, severity) {
                if (typeof(msg) == 'undefined') {
                    severity = ($('.error').length) ? 'error' : ($('.info').length) ? 'info' : null;
                    if (!severity) return;
                } else {
                    severity = severity || 'error';
                    msg = (typeof(msg) == 'function') ? msg() : msg;
                    $('.' + severity).remove();
                    $('#container').append('<div class="' + severity + '">' + msg + '</div>');
                }
                $('.' + severity).each(function() {
                    $(this).addClass(function() {
                        return 'hide alert alert-' + $(this).attr('class');
                    });
                    $(this).prepend('<button type="button" class="close" data-dismiss="alert">×</button>').css('left', ( $(document).innerWidth() - $(this).width() ) / 2 );
                    $(this).fadeIn('slow');
                });
            },

            modal: function(options) {
                $('.modal').remove();
                options.footer = options.footer || '';
                var $modal = $(_.template($('#template-modal').html(), options)).appendTo('#container');
                $('.modal').modal();
                xn.helper.stylize('.modal-body');
            }
        }
    };

    $(function() {
        try {
            var ciprofiler = $('#codeigniter_profiler').html();
            $('#codeigniter_profiler').detach();
            $("#profiler_btn").click(function(evt) {
                xn.helper.modal({
                    title: 'Profiler',
                    body: ciprofiler
                });
                return evt.preventDefault();
            });
        } catch (e) {
            console.error(e);
        }

        var run = false;

        $(window).focus(function() {
            if (location.pathname.indexOf('user/unauthorized') >= 0 || location.pathname.indexOf('user/login') >= 0) return;
            if (!run) return;
            run = true;
            if (location.origin + location.pathname == xn.helper.siteUrl('user/login')) return;
            $.get(xn.helper.siteUrl('user/check_session.json'), function(data) {
                if (!data) {
                    run = false;
                    location.href = xn.helper.siteUrl('user/unauthorized') + '?continue=' + location.href;
                }
            });
        });

        $('.widget').each(function() {
            var $widget = $(this);
            // var $widgetPlaceholder = $widget;//$('<div class="span12 widget-container"/>');
            // $widget.append($widgetPlaceholder).addClass('row-fluid');

            var fn = function() {
                xn.helper.stylize(this);
                $('.refresh', this).click(function() {
                    $widget.load($widget.attr('data-url'), fn);
                });
            };
            $widget.load($widget.attr('data-url'), fn);
        });
    });

    return xn;
})();

/******************************************************************************/


xn.helper.stylize();

function timer() {
    var t = new Date();
    $('.system-datetime').html(t.format('default'));
    $('.xinix-time').html(t.format('xinixTime'));
    $('.xinix-date').html(t.format('xinixDate'));
}
setInterval(timer, 1000);
timer();

prettyPrint();

$('a.btn-modal').live('click', function(evt) {
    evt.preventDefault();
    var href = $(this).attr('href');
    var title = $(this).attr('data-title') || $(this).html();
    $.get(href, function(data) {
        xn.helper.modal({
            title: title,
            body: data
        });
    });


    return false;
});

$(function() {
    $(window).bind('resize', function() {
        $('#container').css('padding-top', $('.navbar-fixed-top').height() + 18);
    });
});