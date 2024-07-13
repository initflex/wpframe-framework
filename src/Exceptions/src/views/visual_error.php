<?php

use Wpframe\Sys\Exceptions\ErrorHandler;
use Wpframe\Sys\Exceptions\visualError;

$appConfig = ErrorHandler::$appConf;
$request = VisualError::$request;
$stackTrace = isset(visualError::$getError['stack_trace_data']) ? visualError::$getError['stack_trace_data'] : [];
$getAllCookies = getAllCookies();
$getAllSessions = getAllSessions();
$getPosts = isset($_POST) ? $_POST : [];
$getFiles = isset($_FILES) ? $_FILES : [];
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

<style>
    <?php
    echo file_get_contents(__DIR__ . '/assets/purecss/pure.min.css');
    echo file_get_contents(__DIR__ . '/assets/purecss/grids-responsive-min.css');
    echo file_get_contents(__DIR__ . '/assets/highlight/styles/atom-one-dark.min.css');
    echo file_get_contents(__DIR__ . '/assets/purecss/custom.css');
    ?>
</style>
<script>
    <?php echo file_get_contents(__DIR__ . '/assets/highlight/highlight.min.js'); ?>
    <?php echo file_get_contents(__DIR__ . '/assets/highlight/languages/php.min.js'); ?>
</script>

<div class="container" id="wpfDebugBar">
    <div class="pure-g">
        <div class="pure-u-1 pure-u-md-4-24"></div>
        <div class="pure-u-1 pure-u-md-16-24">
            <div class="pure-g wpf-header-error wpf-spacing-tb-2 bar-error-message pd-tb pd-lr">
                <div class="pure-u-1 pure-u-md-6-24">
                    <center>
                        <a href="https://initflex.com/docs/wpframe/" class="wpf-link-menu-item" target="_blank">Read the Documentation</a> --&gt;
                    </center>
                </div>
            </div>
            <!-- error mesage -->
            <div class="pure-g bg-medium-transparent wpf-spacing-tb-2 bar-error-message pd-tb pd-lr">
                <div class="pure-u-1 pure-u-md-18-24">
                    <span class="text-title"><?php echo visualError::$getError['type_name']; ?></span>
                    <div class="bar-error-message-info">
                        <div class="bar-error-message-content" id="limit-show-error-info-id" show-error-method="all">
                            <p class="text-bold-2 font-size-3">
                                <?php echo htmlentities(visualError::$getError['message']); ?>
                            </p>
                        </div>
                        <div class="bar-error-message-toggle" onclick="showAllErrMessageInfo()">Show Less</div>
                    </div>
                </div>
                <div class="pure-u-1 pure-u-md-6-24 text-right">
                    <span class="label-error font-size-0 wpf-mr">PHP <?php echo wpf_php_version(); ?></span>
                    <span class="label-error font-size-0">WPFrame <?php echo WPFP_VERSION; ?></span>
                </div>
            </div>
            <!-- code view -->
            <div class="pure-g bg-medium-transparent wpf-spacing-tb-2 bar-error-message">
                <div class="pure-u-1 pure-u-md-6-24">
                    <div class="side-bar-stack-trace">
                        <div class="text-title-1 pd-a">Stack Trace</div>
                        <div class="stack-trace-list max-height-scroll">
                            <div class="stack-trace-item active" id="stack_trace_0" onclick="showStackTraceItem(
                                    0, 
                                    `<?php echo duplicateBackSlash(htmlentities(visualError::$getError['file'])); ?>`, 
                                    `<?php echo visualError::$getError['line']; ?>`
                                )">
                                <?php echo visualError::$getError['file'] . ':' . visualError::$getError['line']; ?>
                            </div>
                            <?php
                            $x = 1;
                            foreach ($stackTrace as $stackTraceItem) {
                                $st_file_path = $stackTraceItem['file_path'];
                                $st_line = $stackTraceItem['line'];
                                $st_line_prefix = $stackTraceItem['line'] !== '' ? ':' . $st_line : $st_line;
                            ?>
                                <div class="stack-trace-item" id="stack_trace_<?php echo $x; ?>" onclick="showStackTraceItem(
                                    <?php echo $x; ?>, 
                                    `<?php echo duplicateBackSlash(htmlentities($st_file_path)); ?>`, 
                                    `<?php echo $st_line; ?>`
                                )">
                                    <?php echo $st_file_path . $st_line_prefix; ?>
                                </div>
                            <?php $x++;
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="pure-u-1 pure-u-md-18-24">
                    <div class="pure-g pd-a">
                        <div class="pure-u-1 pure-u-md-21-24 text-bold-2">
                            <span id="cp-file-path"><?php echo visualError::$getError['file']; ?></span>
                        </div>
                        <div class="pure-u-1 pure-u-md-3-24 text-right text-bold-2">
                            <span id="cp-line"><?php echo visualError::$getError['line']; ?></span>
                        </div>
                    </div>
                    <div class="pd-l-2 pd-r-2 pd-b">
                        <pre id="code-preview-0" class="cp-item"><code class="language-php"><?php echo visualError::$getContentsLines['contents']; ?></code></pre>
                        <?php
                        $y = 1;
                        $set_top_num = [];
                        foreach ($stackTrace as $stackTracePerItem) {
                            $st_file_set = $stackTracePerItem['file_path'];
                            $st_content_set = $st_file_set;
                            $set_top_num[$y] = 0;
                            if (file_exists($st_file_set)) {
                                $get_st_content = wpf_get_contents_lines(
                                    htmlentities(file_get_contents($st_file_set)),
                                    $stackTracePerItem['line'],
                                    12,
                                    12
                                );
                                $st_content_set = isset($get_st_content['contents']) ? $get_st_content['contents'] : $st_file_set;
                                $set_top_num[$y] = isset($get_st_content['top_num']) ? $get_st_content['top_num'] : 0;
                            }
                        ?>
                            <pre id="code-preview-<?php echo $y; ?>" class="cp-item"><code class="language-php"><?php echo $st_content_set; ?></code></pre>
                        <?php $y++;
                        } ?>
                    </div>
                </div>
            </div>
            <!-- HTTP Request info -->
            <div class="pure-g bg-medium-transparent wpf-spacing-tb-2 bar-error-message">
                <div class="pure-u-1 pure-u-md-1-1">
                    <div class="text-title-2 title-header pd-a">Request</div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-b pd-r">
                    <div class="wpf-list-item item-has-sub">
                        <div>URL</div>
                        <div><?php echo $request->getFullUrl(); ?></div>
                    </div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-r">
                    <div class="text-title-1">Browser</div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-b pd-r">
                    <div class="wpf-list-item item-has-sub">
                        <div>User Agent</div>
                        <div><?php echo $request->userAgent(); ?></div>
                    </div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-r">
                    <div class="text-title-1">Headers</div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-b pd-r">
                    <?php foreach (getallheaders() as $headerItem => $headerValue) { ?>
                        <div class="wpf-list-item item-has-sub">
                            <div><?php htmlentities(print_r($headerItem)); ?></div>
                            <div><?php htmlentities(print_r($headerValue)); ?></div>
                        </div>
                    <?php } ?>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-r">
                    <div class="text-title-1">Body - POST</div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-b pd-r">
                    <?php if (count($getPosts) == 0) { ?>
                        <div class="wpf-list-item">
                            <div class=" wpf-marked wpf-text-muted">Empty</div>
                        </div>
                    <?php } ?>
                    <?php foreach ($getPosts as $postItem => $postValue) { ?>
                        <div class="wpf-list-item item-has-sub">
                            <div><?php htmlentities(print_r($postItem)); ?></div>
                            <div><?php htmlentities(print_r($postValue)); ?></div>
                        </div>
                    <?php } ?>
                </div>

                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-r">
                    <div class="text-title-1">Body - FILES</div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-b pd-r">
                    <?php if (count($getFiles) == 0) { ?>
                        <div class="wpf-list-item">
                            <div class=" wpf-marked wpf-text-muted">Empty</div>
                        </div>
                    <?php } ?>
                    <?php foreach ($getFiles as $fileItem => $fileValue) { ?>
                        <div class="wpf-list-item item-has-sub">
                            <div><?php htmlentities(print_r($fileItem)); ?></div>
                            <div><?php htmlentities(print_r($fileValue)); ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- Cookies & Sessions info -->
            <div class="pure-g bg-medium-transparent wpf-spacing-tb-2 bar-error-message">
                <div class="pure-u-1 pure-u-md-1-1">
                    <div class="text-title-2 title-header pd-a">Cookies & Sessions</div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-b pd-r">
                    <?php foreach ($getAllCookies as $cookieItem => $valCookieItem) { ?>
                        <div class="wpf-list-item item-has-sub">
                            <div><?php htmlentities(print_r($cookieItem)); ?></div>
                            <div><?php htmlentities(print_r($valCookieItem)); ?></div>
                        </div>
                    <?php } ?>
                    <?php foreach ($getAllSessions as $sessionItem => $valSessionItem) { ?>
                        <div class="wpf-list-item item-has-sub">
                            <div><?php htmlentities(print_r($sessionItem)); ?></div>
                            <div><?php htmlentities(print_r($valSessionItem)); ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- Context Info -->
            <div class="pure-g bg-medium-transparent wpf-spacing-tb-2 bar-error-message">
                <div class="pure-u-1 pure-u-md-1-1">
                    <div class="text-title-2 title-header pd-a">Context</div>
                </div>
                <div class="pure-u-1 pure-u-md-1-1 pd-l pd-b pd-r">
                    <div class="wpf-list-item item-has-sub">
                        <div>PHP Version</div>
                        <div><?php echo wpf_php_version(); ?></div>
                    </div>
                    <div class="wpf-list-item item-has-sub">
                        <div>WordPress Version</div>
                        <div><?php echo wpf_wp_version(); ?></div>
                    </div>
                    <div class="wpf-list-item item-has-sub">
                        <div>WPFrame Version</div>
                        <div><?php echo WPFP_VERSION; ?></div>
                    </div>
                    <div class="wpf-list-item item-has-sub">
                        <div>WPFrame Env</div>
                        <div>
                            <?php echo $appConfig['env'] ? $appConfig['env'] : 'false'; ?>
                        </div>
                    </div>
                    <div class="wpf-list-item item-has-sub">
                        <div>WPFrame Debug</div>
                        <div>
                            <?php echo $appConfig['app_debug'] ? 'true' : 'false'; ?>
                        </div>
                    </div>
                    <div class="wpf-list-item item-has-sub">
                        <div>WordPress Debug</div>
                        <div>
                            <?php echo null !== WP_DEBUG ?
                                (WP_DEBUG == true ? 'true' : 'false') : 'WordPress Debug not Active.'; ?>
                        </div>
                    </div>
                    <div class="wpf-list-item item-has-sub">
                        <div>WordPress Locale</div>
                        <div>
                            <?php echo get_locale(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pure-u-1 pure-u-md-4-24"></div>
    </div>
</div>

<div class="wpf-error-show-hide">
    <center>
        <button class="button-error pure-button" onclick="wpfVisualErrorShowHide()">WPFrame Debug</button>
    </center>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', (event) => {
        var codeElement = document.getElementById('code-preview-0');
        hljs.highlightElement(codeElement);
        let topNum = <?php echo visualError::$getContentsLines['top_num']; ?>;
        let highlightNum = <?php echo visualError::$getError['line']; ?>;
        filterContentPerLinePC(codeElement, highlightNum, true, topNum);

        <?php $z = 1;
        foreach ($stackTrace as $stackTracePerItem) { ?>
            var stackCodePreview_<?php echo $z; ?> = document.getElementById('code-preview-<?php echo $z; ?>');
            stackCodePreview_<?php echo $z; ?>.style.display = 'none';
            stackCodePreview_<?php echo $z; ?>.classList.add('d-none');

            hljs.highlightElement(stackCodePreview_<?php echo $z; ?>);
            let topNum_<?php echo $z; ?> = <?php echo isset($set_top_num[$z]) ? $set_top_num[$z] : 0; ?>;
            let highlightNum_<?php echo $z; ?> = <?php echo trim($stackTracePerItem['line']) ? $stackTracePerItem['line'] : 0 ?>;
            filterContentPerLinePC(stackCodePreview_<?php echo $z; ?>, highlightNum_<?php echo $z; ?>, true, topNum_<?php echo $z; ?>);

        <?php $z++;
        } ?>
    });

    <?php
    echo file_get_contents(__DIR__ . '/assets/debug.js');
    ?>
</script>