## Subresource Integrity

If you are loading Highlight.js via CDN you may wish to use [Subresource Integrity](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity) to guarantee that you are using a legimitate build of the library.

To do this you simply need to add the `integrity` attribute for each JavaScript file you download via CDN. These digests are used by the browser to confirm the files downloaded have not been modified.

```html
<script
  src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"
  integrity="sha384-9mu2JKpUImscOMmwjm1y6MA2YsW3amSoFNYwKeUHxaXYKQ1naywWmamEGMdviEen"></script>
<!-- including any other grammars you might need to load -->
<script
  src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/go.min.js"
  integrity="sha384-WmGkHEmwSI19EhTfO1nrSk3RziUQKRWg3vO0Ur3VYZjWvJRdRnX4/scQg+S2w1fI"></script>
```

The full list of digests for every file can be found below.

### Digests

```
sha384-JBkI+6623OoC1qCgG+MY/Ta0qRYSzTDH4NGMA+7U8RNOjkh7geFvYpRidvdHs3zT /es/languages/php.js
sha384-6Znh5S5q9F0hGNMtdaihFL3RJeMqL1a/UecjgzaBrhoxe4sjd5aiEOgokt7blhSW /es/languages/php.min.js
sha384-lbDR+U/5ETRHb0r5ElxxVnAt95EFhleDFZw7nkVPcRk3lbX9NsT/Ppg37FFNiv8+ /es/languages/php-template.js
sha384-dGJwrvnfo/JaY0lSDtJuhB6XXPmL9hSqOnIYndlN0eE1PoA9FFxd+VtaSfcacNDA /es/languages/php-template.min.js
sha384-S1JDGPScVg8ikNKLZc4CSP0ZxLiJ7bOJMzTLfOzQiCxR6wPqAa+YtauHJXQpc2GV /languages/php.js
sha384-c1RNlWYUPEU/QhgCUumvQSdSFaq+yFhv3VfGTG/OTh8oirAi/Jnp6XbnqOLePgjg /languages/php.min.js
sha384-56Iyz0oX8ZJZPmm/9pflGG2cVp/0NER9AEbckNsLoTXXK5uMPpVWUkf/WL1BPzEb /languages/php-template.js
sha384-i5dGKRLoml9823i21v5ltbNFQCB7OAwkUve4TaJrwSnXRQ1kOjT+Wa8jX/EOj434 /languages/php-template.min.js
sha384-WmY9VX3plPoy7D4ci/7kLczv/gWxjAB8yN0dirSvpf181cyy73di044761jX5sNp /highlight.js
sha384-2+3dI2aRps4xExXSXWw2d+RcQ5o6tR092BqEcRfNrGcsEHkI2w0FAn3ez/FChY/E /highlight.min.js
```

