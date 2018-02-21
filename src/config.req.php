<?php
define("ABSPATH", __DIR__);

define("DB_USER", "mangadex");
define("DB_PASSWORD", "root");
define("DB_NAME", "mangadex");
define("DB_HOST", "localhost");

define("TITLE", "MangaDex");
define("DESCRIPTION", "A manga reader for scanlation groups.");

define("SMTP_USER", "mangadex.org@gmail.com");
define("SMTP_PASSWORD", "root");

define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", 587);
define("SMTP_BCC", "anidex.moe@gmail.com");

define("FADE_DURATION", 3000);

define("CACHE_TIME", 60); //seconds

$caching = 1; //to cache or not to cache that is the question
define("MAX_CHAPTER_FILESIZE", 104857600); //100*1024*1024

$themes = array(1 => "Light", 2 => "Dark");
$orig_lang_array = array(2 => "Japanese", 12 => "Vietnamese", 21 => "Chinese", 28 => "Korean", 32 => "Thai", 34 => "Filipino");
$status_array = array(1 => "Ongoing", 2 => "Completed");

$allowed_chapter_ext = array("zip", "cbz");
$allowed_image_ext = array("jpg", "jpeg", "png", "gif");
define("MAX_IMAGE_FILESIZE", 1048576);
define("ALLOWED_IMG_EXT", array("jpg", "jpeg", "png", "gif"));
define("ALLOWED_MIME_TYPES", array("image/png", "image/jpeg", "image/gif"));

$bbcode_array_title = array("Bold", "Italics", "Underline", "Strikethrough", "Align left", "Align centre", "Align right", "Image", "Hyperlink", "Superscript", "Subscript", "Unordered list", "Ordered list", "Horizontal rule", "Spoiler", "Code", "Quote");
$bbcode_array_id = array("bold", "italic", "underline", "strikethrough", "align-left", "align-center", "align-right", "image", "link", "superscript", "subscript", "list-ul", "list-ol", "arrows-alt-h", "eye-slash", "code", "quote-left");
?>
