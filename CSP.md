connect-src 'self';
font-src 'self' https://vimeo.com;
frame-src 'self' https://vimeo.com;
img-src 'self';
manifest-src 'self';
media-src 'self';
object-src 'self';
script-src https://vimeo.com;
style-src 'self' https://vimeo.com;
worker-src 'self';

default-src 'self' 'unsafe-inline' vimeo.com *.vimeocdn.com *.vimeo.com youtube.com *.youtube.com youtu.be *.youtu.be;
frame-src 'self' 'unsafe-inline' vimeo.com *.vimeocdn.com *.vimeo.com youtube.com *.youtube.com youtu.be *.youtu.be;
font-src 'self' vimeo.com *.vimeocdn.com *.vimeo.com;