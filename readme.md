# LoremFlickr
LoremFlickr provides placeholder images for every case, web or print, on almost any subject, in any size. Visit https://loremflickr.com to see this in action.
## How to install
Put the files in the location of your choice. Enter your Flickr API and server details in config.php. Perhaps adjust the cache locations and make sure those folders are server-writable. 

Inside the includes folder, add dopiaza's DPZFlickr. You only need the file Flickr.php.

You might want to add a cronjob to clean the cache of old files.

Depending on where you've put your files, you might need to update the .htaccess file to make sure redirects point to image.php in the right folder.
## How to use
Point your browser to, depending on where you put the files, http://your-website.com/g/320/240/paris,girl/all

For more details, visit https://loremflickr.com
## Credits
+ LoremFlickr is maintained by Babak Fakhamzadeh, https://babakfakhamzadeh.com. On Flickr,  https://www.flickr.com/photos/mastababa/
+ The image resize function was adapted from here: https://stackoverflow.com/a/747277/1374538	
+ DPZFlickr is maintained by dopiaza: https://github.com/dopiaza/DPZFlickr