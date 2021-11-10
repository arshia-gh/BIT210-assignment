# Includes Documentation
&rarr; All includes utilize constants defined in app_metadata.inc.php

### footer template usage
<pre>
// include app metadata
include('./includes/app_metadata.inc.php')
// include footer template
include('./includes/footer.inc.php')
</pre>
### navbar template usage
<pre>
// include app metadata
include('./includes/app_metadata.inc.php')
// define nav links
$nav_links = [
    'home' => ['./index.php', true],
    'dashboard' => ['./patient/index.php', false]
]
// include footer template
include('./includes/footer.inc.php')
</pre>
### table generator usage
<pre>
// todo
</pre>