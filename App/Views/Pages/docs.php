    <link rel="stylesheet" type="text/css" href="<?php $this->publicUrl('/dist/swagger-ui.css')?>" />
    <link rel="icon" type="image/png" href="<?php $this->publicUrl('/dist/favicon-32x32.png')?>" sizes="32x32" />
    <link rel="icon" type="image/png" href="<?php $this->publicUrl('/dist/favicon-16x16.png')?>" sizes="16x16" />
    <style>
        html
        {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *,
        *:before,
        *:after
        {
            box-sizing: inherit;
        }

        body
        {
            margin:0;
            background: #fafafa;
        }
    </style>

<div id="swagger-ui"></div>

<script src="<?php $this->publicUrl('/dist/swagger-ui-bundle.js')?>" charset="UTF-8"> </script>
<script src="<?php $this->publicUrl('/dist/swagger-ui-standalone-preset.js')?>" charset="UTF-8"> </script>
<script>
    window.onload = function() {
        // Begin Swagger UI call region
        const ui = SwaggerUIBundle({
            url: `${SITE_URL}/swagger/json`,
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout"
        });
        // End Swagger UI call region

        window.ui = ui;
    };
</script>
