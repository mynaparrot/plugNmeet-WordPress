=== plugnmeet ===
Contributors: mynaparrot
Donate link: https://www.plugnmeet.org/
Tags: mynaparrot, web conference, plugnmeet
Requires at least: 5.9
Tested up to: 6.6.2
Stable tag: 1.2.11
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WebRTC based Scalable, High Performance, Open source web conferencing system for Wordpress.

== Description ==

WebRTC based Scalable, High Performance, Open source web conferencing system. Using this plugin, you will be able to load the [plugNmeet-client](https://github.com/mynaparrot/plugNmeet-client) interface **directly within your Wordpress site**. You will not be redirected to a third-party website. You will also be able to **easily customize** the conference room interface.

**Features:**

* Compatible with all devices. Browser recommendation: **Google Chrome, Firefox**. For **iOS**: Safari.
* WebRTC based secured & encrypted communication.
* Scalable and high performance system written in Go programming language which made it possible to distributed as a single binary file!
* Simulcast and Dynacast features will allow you to continue online conferencing even if your internet connection is slow! Supported video codecs: H264, VP8 and AV1.
* Easy integration with any existing website or system.
* Easy customization with functionality, URL, logo, and branding colors.
* HD audio, video call and Screen sharing. Virtual background for webcams.
* Shared notepad and Whiteboard for live collaboration. Can upload, draw & share various office file (pdf, docx, pptx, xlsx, txt etc.) in whiteboard directly.
* Easy to use Polls & voting.
* Customizable waiting room.
* Various Lock & control settings.
* Easy to configurable Breakout rooms
* Raise hand.
* Public & private chatting with File sharing.
* MP4 Recordings.
* RTMP Broadcasting
* Live speech to text/translation (Powered by [microsoft azure](https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/get-started-text-to-speech?pivots=programming-language-go&tabs=linux%2Cterminal#prerequisites))
* End-to-End encryption (**E2EE**) (Supported browsers: browser based on Chromium 83, Google Chrome, Microsoft Edge, Safari, firefox 117+).
* A detailed **analytics report** to assess students' performance in the online classroom.

= Note =

This plugin will require the [plugNmeet-server](https://github.com/mynaparrot/plugNmeet-server) to be up and running. Plug-N-Meet is a free and open source project. You can obtain plugNmeet-server by either:

1) Create your own plugNmeet-server by following the [installation instructions](https://www.plugnmeet.org/docs/installation); Or

2) Use a ready-to-use [plugNmeet cloud solution](https://www.plugnmeet.cloud).

== Installation ==

1. Server setup: https://www.plugnmeet.org/docs/installation
2. Plugin configuration: https://www.plugnmeet.org/docs/user-guide/wordPress-integration

== Frequently Asked Questions ==

= How to use shortcode? =

After create room save it. Now enter to re-edit. You'll get shortcode there.

= How to setup server? =

This plugin will require the [plugNmeet-server](https://github.com/mynaparrot/plugNmeet-server) to be up and running. Plug-N-Meet is a free and open source project. You can obtain plugNmeet-server by either:

1) Create your own plugNmeet-server by following the [installation instructions](https://www.plugnmeet.org/docs/installation); Or

2) Use a ready-to-use [plugNmeet cloud solution](https://www.plugnmeet.cloud).

== Screenshots ==

1. Whiteboard
2. Shared notepad

== Changelog ==
= 1.2.10 =
* bump SDK
* option to set auto generate user id
* option to configure copyright info

= 1.2.9 =
* bump SDK
* option to encrypt chat & whiteboard data

= 1.2.8 =
* bump SDK

= 1.2.7 =
* bump SDK
* feat: option to disable virtualBackgrounds & raiseHand

= 1.2.6 =
* bump SDK
* feat: option to set audio preset, default: music e.g Bitrate 32000

= 1.2.5 =
* bump SDK
* feat: added option to enable analytics

= 1.2.4 =
* feat: E2EE

= 1.2.3 =
* feat: Speech to text/translation

= 1.2.1 =
* ingress
* moderator join first
* camera position

= 1.2.0 =
* auto recording + playback recording
* new permission setting

= 1.1.0 =
* Bump Plug-N-Meet PHP SDK to v1.1.0

**Note:** Require plugNmeet-server [v1.2.2 ](https://github.com/mynaparrot/plugNmeet-server/releases/tag/v1.2.2) or later

= 1.0.12 =
* feat: load client from remote

= 1.0.10 =
* refact: frontend enhancement
* Update client to v1.1.6

= 1.0.9 =
* feat: Display external link features
* Update client to v1.1.5

= 1.0.7 =
* new server's compatibility
* Feat: Breakout rooms + room duration
* Feat: Polls & private chat

= 1.0.6 =
* new server's compatibility

= 1.0.5 =
* impl: new design customization

= 1.0.4 =
* impl: Lock settings for whiteboard & shared notepad
* Can set video_codec
* Can set resolution for webcam & share screen
* Improvement in design customization

= 1.0.3 =
* Feat: Interface customization
* But fixed & improvement

= 1.0.2 =
* Feat: Whiteboard & Shared notepad
* But fixed & improvement

= 1.0.1 =
* But fixed & improvement

= 1.0.0 =
* Initial release

