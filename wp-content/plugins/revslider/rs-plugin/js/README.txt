Edited both of these files and did a find/replace of "a.vimeocdn.com" with "f.vimeocdn.com":
   jquery.themepunch.revolution.min.js
   jquery.themepunch.revolution.js

Reason: a.vimeocdn.com has an invalid SSL certificate so you can't fetch the script over SSL. f.vimeocdn.com has a valid SSL cert.
