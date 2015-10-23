<?php

/**
 * index.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

?>

<h1>This is a test page for Arch Theme</h1>
<p>Every contents should be inside div#content to be styled like this. It have default <strong>13px</strong> font size and <strong>Varela</strong> font family as it's default font. Find this HTML source code in arch/view/site/index.php.<p>


<hr />

<div id="layouting-example">
    <h1>Layouting Example</h1>

    <p>This examples on how to use .layout-flexible. It's a div but act like a table.</p>
    <div class="layout-flexible">
        <div class="fifths">.fifths</div>
        <div class="quarter">.quarter</div>
        <div class="half">.half</div>
        <div class="auto">.auto</div>
    </div>
    <div class="layout-flexible">
        <div class="fifths">.fifths</div>
        <div class="fifths">.fifths</div>
        <div class="fifths">.fifths</div>
        <div class="fifths">.fifths</div>
        <div class="fifths">.fifths</div>
    </div>
    <div class="layout-flexible">
        <div class="quarter">.quarter</div>
        <div class="quarter">.quarter</div>
        <div class="quarter">.quarter</div>
        <div class="quarter">.quarter</div>
    </div>
    <div class="layout-flexible">
        <div class="half">.half</div>
        <div class="half">.half</div>
    </div>
    <div class="layout-flexible">
        <div class="quarter">.quarter</div>
        <div class="quarter">.quarter</div>
        <div class="auto">.auto</div>
    </div>
    <div class="layout-flexible">
        <div class="thirds">.thirds</div>
        <div class="thirds">.thirds</div>
        <div class="thirds">.thirds</div>
    </div>

    <p>This examples on how to use .layout-flexible inside .layout-flexible for .layout-flexible-ception.</p>
    <div class="layout-flexible">
        <div class="half">
            <p>.half <em>below is example for .layout-flexible inside .layout-flexible</em></p>
            <div class="layout-flexible">
                <div class="half">.half</div>
                <div class="half">.half</div>
            </div>
        </div>
        <div class="auto">
            <p>.half <em>below is another example for .layout-flexible inside .layout-flexible</em></p>
            <div class="layout-flexible">
                <div class="auto">.auto</div>
                <div class="auto">.auto</div>
                <div class="auto">.auto</div>
            </div>
        </div>
    </div>
</div>

<hr />

<div id="typography-example">
    <h1>Typography Examples</h1>

    <div class="layout-flexible">
        <div class="thirds">
            <p>This examples on how to use heading</p>
            <h1>Heading 1: h1</h1>
            <span class="h1">Heading 1: .h1</span>
            <h2>Heading 2: h2</h2>
            <span class="h2">Heading 2: .h2</span>
            <h3>Heading 3: h3</h3>
            <span class="h3">Heading 3: .h3</span>
            <h4>Heading 4: h4</h4>
            <span class="h4">Heading 4: .h4</span>
        </div>
        <div class="thirds">
            <p><strong>Sans Serif font</strong>Aenean sit<sub>22</sub> amet risus quis enim <a href="#">ultrices</a> mattis sit amet eget arcu. <strong>Integer commodo vulputate nisl</strong>, eu feugiat odio cursus auctor. Pellentesque egestas dui hendrerit massa <u>faucibus ut dapibus</u> tellus malesuada. <em>Lorem ipsum dolor</em> sit amet, consectetur adipiscing elit. Mauris in lectus metus. Duis risus orci, sodales tristique iaculis nec, adipiscing non sapien. Vestibulum nec purus id mauris laoreet tincidunt. Morbi vulputate ornare hendrerit. Sed eget turpis id tellus bibendum molestie quis et velit.</p>
            <p class="serif"><strong>Serif font</strong>, justo<sup>22</sup> congue <u>elementum pulvinar</u>, erat metus pretium sem, at <em>sollicitudin justo elit in ligula</em>. Praesent tempus, velit sed blandit mattis, massa mi lacinia dolor, <strike>ut malesuada tortor nibh ac nulla</strike>. Duis volutpat dui eget erat molestie ac mollis lectus tristique. Nulla sit amet risus neque. <small>Etiam feugiat libero sapien</small>. Aenean sapien turpis, <a href="#">pellentesque a aliquam sit amet</a>, tempus eu massa. Nulla pellentesque quam vitae nunc lacinia id dictum est malesuada.</p>
        </div>
        <div class="thirds">
            <ul>
                <li>Curabitur eget massa nisi, at elementum enim.</li>
                <li>Donec ultricies diam id turpis elementum vel pharetra turpis venenatis.</li>
                <li>Nam metus dui, vestibulum a ullamcorper sed, pretium et nibh.
                    <ol>
                        <li>Cras molestie nulla suscipit neque placerat et vehicula velit euismod.</li>
                        <li>Pellentesque sit amet odio vitae lacus tincidunt tempus id nec quam.</li>
                        <li>Vestibulum a tempus augue.</li>
                    </ol>
                </li>
                <li>Phasellus a lacus ut sapien varius cursus eget id arcu.</li>
                <li>Integer tempor auctor libero quis faucibus. Vivamus quis blandit nibh.</li>
                <li>Morbi nec enim a felis scelerisque eleifend.</li>
                <li>Duis ut est nec nibh aliquet accumsan et a velit. Nulla facilisi.</li>
            </ul>
        </div>
    </div>
</div>

<hr />

<div id="precode-example">
    <h1>Pre Code Examples</h1>

    <div class="layout-flexible">
        <div class="half">
            <code class="prettyprint">
                /* Code tag example, LESS Variable */<br />
                <br />
                @black:#000;<br />
                @gray-darker:lighten(@black, 10%);<br />
                @gray-dark:lighten(@black, 25%);<br />
                @gray:lighten(@black, 50%);<br />
                @gray-light:lighten(@black, 70%);<br />
                @gray-lighter:lighten(@black, 90%);<br />
                <br />
                @white:#fff;<br />
                <br />
                @cyan:#08b5fb;<br />
                <br />
                @orange:#F93;<br />
                @orange-light:lighten(@orange, 15%);<br />
                @orange-dark:darken(@orange, 20%);<br />
                <br />
                @blue:#069;<br />
                @blue-light:lighten(@blue, 15%);<br />
                @blue-dark:darken(@blue, 20%);
            </code>
        </div>
        <div class="half">
            <pre class="prettyprint">
/* Pre tag example, LESS - CSS Mixins */

.border-radius (@var:5px) {
    border-radius:@var;
    -o-border-radius:@var;
    -ms-border-radius:@var;
    -moz-border-radius:@var;
    -khtml-border-radius:@var;
    -webkit-border-radius:@var;
}

.box-shadow (@var) {
    box-shadow:@var;
    -o-box-shadow:@var;
    -ms-box-shadow:@var;
    -moz-box-shadow:@var;
    -khtml-box-shadow:@var;
    -webkit-box-shadow:@var;
}
            </pre>
        </div>
    </div>
</div>

<hr />

<div id="table-example">
    <h1>Tabular Examples</h1>

    <h3>Most common table: table.grid</h3>
    <table class="grid table table-hover table-striped table-condensed">
        <tr>
            <th class="x-short">#</th>
            <th class="long">First Coloumn</th>
            <th class="long">Second Coloumn</th>
            <th class="auto">3<sup>rd</sup> Coloumn</th>
        </tr>
        <tr>
            <td class="x-short">1</td>
            <td class="long">First Content on 1<sup>st</sup> row</td>
            <td class="long">Second Content on 1<sup>st</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 1<sup>st</sup> row</td>
        </tr>
        <tr>
            <td class="x-short">2</td>
            <td class="long">First Content on 2<sup>nd</sup> row</td>
            <td class="long">Second Content on 2<sup>nd</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 2<sup>nd</sup> row</td>
        </tr>
        <tr>
            <td class="x-short">3</td>
            <td class="long">First Content on 3<sup>rd</sup> row</td>
            <td class="long">Second Content on 3<sup>rd</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 3<sup>rd</sup> row</td>
        </tr>
    </table>

    <h3>Second table style: table.grid.zebra</h3>
    <table class="grid zebra">
        <tr>
            <th class="x-short">#</th>
            <th class="long">First Coloumn</th>
            <th class="long">Second Coloumn</th>
            <th class="auto">3<sup>rd</sup> Coloumn</th>
        </tr>
        <tr>
            <td class="x-short">1</td>
            <td class="long">First Content on 1<sup>st</sup> row</td>
            <td class="long">Second Content on 1<sup>st</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 1<sup>st</sup> row</td>
        </tr>
        <tr>
            <td class="x-short">2</td>
            <td class="long">First Content on 2<sup>nd</sup> row</td>
            <td class="long">Second Content on 2<sup>nd</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 2<sup>nd</sup> row</td>
        </tr>
        <tr>
            <td class="x-short">3</td>
            <td class="long">First Content on 3<sup>rd</sup> row</td>
            <td class="long">Second Content on 3<sup>rd</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 3<sup>rd</sup> row</td>
        </tr>
    </table>

    <h3>Second table style: table.grid.stripe</h3>
    <table class="grid stripe">
        <tr>
            <th class="x-short">#</th>
            <th class="long">First Coloumn</th>
            <th class="long">Second Coloumn</th>
            <th class="auto">3<sup>rd</sup> Coloumn</th>
        </tr>
        <tr>
            <td class="x-short">1</td>
            <td class="long">First Content on 1<sup>st</sup> row</td>
            <td class="long">Second Content on 1<sup>st</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 1<sup>st</sup> row</td>
        </tr>
        <tr>
            <td class="x-short">2</td>
            <td class="long">First Content on 2<sup>nd</sup> row</td>
            <td class="long">Second Content on 2<sup>nd</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 2<sup>nd</sup> row</td>
        </tr>
        <tr>
            <td class="x-short">3</td>
            <td class="long">First Content on 3<sup>rd</sup> row</td>
            <td class="long">Second Content on 3<sup>rd</sup> row</td>
            <td class="auto">3<sup>rd</sup> Content on 3<sup>rd</sup> row</td>
        </tr>
    </table>
</div>

<?php $USER = $CI->auth->get_user() ?>
<?php if (!$USER['is_login']): ?>
<hr />

<a href="<?php echo site_url('user/login') ?>">Click here to login</a> or
<a href="<?php echo site_url('user/login/facebook') ?>">here to facebook login</a> or
<a href="<?php echo site_url('user/login/twitter') ?>">here to twitter login</a>
<?php endif ?>
