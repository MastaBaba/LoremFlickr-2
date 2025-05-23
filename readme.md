# LoremFlickr

LoremFlickr provides placeholder images for every case, web or print, on almost any subject, in any size. Visit https://loremflickr.com for more details.

## How to install

Put the files in the location of your choice. Enter your Flickr API and server details in config.php. Perhaps adjust the cache locations and make sure those folders are server-writable. 

Inside the includes folder, add dopiaza's DPZFlickr. You only need the file Flickr.php.

You might want to add a cronjob to clean the cache of old files.

Depending on where you've put your files, you might need to update the .htaccess file to make sure redirects point to image.php in the right folder.

## How to use

Point your browser to, depending on where you put the files, https://your-website.com/g/320/240/paris,girl/all

## Examples

  https://your-website.com/320/240

A random picture of 320 x 240 pixels. If not supplying any keyword, you'll get a picture matching the keyword kitten.

  https://your-website.com/320/240/dog

A random picture matching the keyword dog, of 320 x 240 pixels.

  https://your-website.com/g/320/240/paris

A random picture in gray matching the keyword paris, of 320 x 240 pixels. Besides g, you can try p, red, green and blue.

  https://your-website.com/320/240/brazil,rio

A random picture matching the keywords brazil *or* rio, of 320 x 240 pixels.

  https://your-website.com/320/240/paris,girl/all

A random picture matching the keywords paris *and* girl, of 320 x 240 pixels.

  https://your-website.com/g/320/240/paris,girl/all

A random picture in grey matching the keywords paris and girl, of 320 x 240 pixels.

If no matching photos are found, the default image, in /assets, is returned.

For more details, visit https://loremflickr.com

## Locking

You can have some control of the image that's displayed. Include a lock query string parameter and give it a value that's a positive integer. While the cache is not updated, and sometimes for longer, the same image will be returned.

  https://your-website.com/320/240?lock=212

## Multiple images on the same page

Your browser might cache the images when you request the same URL multiple times on the same page. You can resolve this by adding a meaningless querystring to the URL. So, for example...

  https://your-website.com/320/240?random=1
  
  https://your-website.com/320/240?random=2
  
  https://your-website.com/320/240?random=3

## Credits

+ LoremFlickr is maintained by Babak Fakhamzadeh, https://babakfakhamzadeh.com. On Flickr,  https://www.flickr.com/photos/mastababa/
+ The image resize function was adapted from here: https://stackoverflow.com/a/747277/1374538	
+ DPZFlickr is maintained by dopiaza: https://github.com/dopiaza/DPZFlickr
