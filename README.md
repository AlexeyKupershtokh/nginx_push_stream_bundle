[![Build Status](https://travis-ci.org/AlexeyKupershtokh/nginx_push_stream_bundle.png?branch=master)](https://travis-ci.org/AlexeyKupershtokh/nginx_push_stream_bundle)
[![Coverage Status](https://coveralls.io/repos/AlexeyKupershtokh/nginx_push_stream_bundle/badge.png)](https://coveralls.io/r/AlexeyKupershtokh/nginx_push_stream_bundle)

nginx_push_stream_bundle
========================
A PHP bundle to assist your nginx_push_stream_module installation:
 1. Generate links on server side.
 2. Publish messages.
 3. Automatically generate ids for messages on server side.
 4. Filter tokens:
   1. Hash token names in order to make them unpredictable by clients.
   2. Prefix tokens to separate your applications sharing the same nginx_push_stream_module locations.
 
Installation
============
`composer require alawar/nginx_push_stream_bundle dev-master`

Usage
=====
In Symfony2 you should register the bundle.
Then add the following config:
```yaml
nginx_push_stream:
  pub_url: http://.../pub?id={token}
  sub_urls:
    polling:      http://.../sub-p/{tokens}
    long-polling: http://.../sub-lp/{tokens}
    streaming:    http://.../sub-s/{tokens}
    eventsource:  http://.../sub-ev/{tokens}
```
