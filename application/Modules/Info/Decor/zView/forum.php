<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_DECOR'),
        'title_href' => $this->getUrl('info/decor'),
        'subtitle' => $translate->sys('LB_SITE_DECOR_FORUM'),
        'subtitle_href' => $this->getUrl('info/decor/forum')
    ));

    echo $this->partial('Basic/Desc/dynamic', array(
        'url' => 'info/decor/forum'
    ));
    ?>
    <p class="indent">&nbsp;</p>
    <section>
        <?php
        echo $this->partial('Basic/title', array(
            'title' => $translate->sys('LB_LATEX_TEXTIT'),
            'num' => 3
        ));
        ?>
        <div class="indent">
            <p>
                <code class="co_attention">&lt;em&gt;</code><?php echo $translate->sys('LB_SAMPLE_TEXT') ?><code class="co_attention">&lt;/em&gt;</code>
            </p>
            <div class="el_border indent">
                <i><?php echo $translate->sys('LB_SAMPLE_TEXT') ?></i>
            </div>
            <p>&nbsp;</p>
        </div>

        <?php
        echo $this->partial('Basic/title', array(
            'title' => $translate->sys('LB_LATEX_TEXTBF'),
            'num' => 3
        ));
        ?>
        <div class="indent">
            <h3><?php echo $translate->sys('LB_LATEX_TEXTBF') ?></h3>
            <p class="indent_vertical">
                <code class="co_attention">&lt;b&gt;</code><?php echo $translate->sys('LB_SAMPLE_TEXT') ?><code class="co_attention">&lt;/b&gt;</code>
            </p>
            <div class="el_border indent">
                <strong><?php echo $translate->sys('LB_SAMPLE_TEXT') ?></strong>
            </div>
            <p>&nbsp;</p>
        </div>

        <?php
        echo $this->partial('Basic/title', array(
            'title' => $translate->sys('LB_LATEX_CENTER'),
            'num' => 3
        ));
        ?>
        <div class="indent">
            <p>
                <code class="co_attention">&lt;center&gt;</code>
                <?php echo $translate->sys('LB_SAMPLE_TEXT') ?>
                <code class="co_attention">&lt;/center&gt;</code>
            </p>
            <div class="el_border indent">
                <center><?php echo $translate->sys('LB_SAMPLE_TEXT') ?></center>
            </div>
            <p>&nbsp;</p>
        </div>

        <?php
        echo $this->partial('Basic/title', array(
            'title' => $translate->sys('LB_LATEX_AUTHOR'),
            'num' => 3
        ));
        ?>
        <div class="indent">
            <h3><?php echo $translate->sys('LB_LATEX_AUTHOR') ?></h3>
            <p>
                <code class="co_attention">&bsol;author{</code>FieryCat<code class="co_attention">}</code>
            </p>
            <div class="el_border indent">
                \author{FieryCat}
            </div>
            <p>&nbsp;</p>
        </div>

        <?php
        echo $this->partial('Basic/title', array(
            'title' => $translate->sys('LB_LATEX_PAGEREF'),
            'num' => 3
        ));
        ?>
        <div class="indent">
            <p>
                <code class="co_attention">&bsol;pageref{</code>index<code class="co_attention">}</code> - <?php echo $translate->sys('LB_LATEX_PAGEREF') ?>
            </p>
            <div class="el_border indent">
                \pageref{index}
            </div>
            <p>&nbsp;</p>
        </div>

        <?php
        echo $this->partial('Basic/title', array(
            'title' => $translate->sys('LB_LATEX_PAGEVIEW'),
            'num' => 3
        ));
        ?>
        <div class="indent">
            <p>
                <code class="co_attention">&bsol;pageview{</code>index<code class="co_attention">}</code> - <?php echo $translate->sys('LB_LATEX_PAGEVIEW') ?>
            </p>
            \pageview{index}
            <p>&nbsp;</p>
        </div>

        <?php
        echo $this->partial('Basic/title', array(
            'title' => $translate->sys('LB_LATEX_TABLE'),
            'num' => 3
        ));
        ?>
        <div class="indent">
            <table>
                <tr>
                    <td>
                        <code class="co_attention">&bsol;table</code>{begin}<br />
                        | 1 | 2 | 3 |<br />
                        | 4 | 5 | 6 |<br />
                        | <code class="co_accepted">&lt;center&gt;</code>
                          <code class="co_accepted">&lt;b&gt;</code>7<code class="co_accepted">&lt;/b&gt;</code>
                          <code class="co_accepted">&lt;/center&gt;</code>
                         <span class="co_attention" title="<?php echo $translate->sys('LB_LATEX_TABLE_COLSPAN') ?>">**|</span><br />
                        <span class="co_attention" title="<?php echo $translate->sys('LB_LATEX_TABLE_ROWSPAN') ?>">|*</span> 8 | 9 | 10 |<br />
                        | 11 | 12 |<br />
                        <code class="co_attention">&bsol;table</code>{end}
                    </td><td valign="top">
                        \table{begin}<br />
                        | 1 | 2 | 3 |<br />
                        | 4 | 5 | 6 |<br />
                        | <center><b>7</b></center> **|<br />
                        |* 8 | 9 | 10 |<br />
                        | 11 | 12 |<br />
                        \table{end}
                    </td>
                </tr>
            </table>
            <p>&nbsp;</p>
        </div>
    </section>
</article>