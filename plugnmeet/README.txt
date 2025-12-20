=== plugnmeet ===
Contributors: mynaparrot
Donate link: https://www.plugnmeet.org/
Tags: video conference, webinar, online meeting, virtual classroom, video chat, zoom alternative, live streaming, webrtc, self-hosted, meeting, video
Requires at least: 5.9
Tested up to: 6.8.2
# x-release-please-start-version
Stable tag: 2.0.0
# x-release-please-end
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Host secure, open-source video meetings and webinars directly on your WordPress site. Keep your users engaged without sending them to a third-party service.

== Description ==

Bring a powerful, open-source video conferencing solution directly into your WordPress site. With plugNmeet, you can host live classes, webinars, and meetings without sending your users to a third-party website, keeping them engaged and on your platform.

The meeting interface is loaded directly on your page for a seamless, native experience. Best of all, the interface is fully customizable to match your brand.

### How It Works

This plugin connects your WordPress site to a plugNmeet server. As a free and open-source project, you have two great options for the server:

1.  **Self-Host:** Create your own server for maximum control and privacy by following the [official installation instructions](https://www.plugnmeet.org/docs/installation).
2.  **Use the Cloud:** Get started in minutes with a ready-to-use [plugNmeet cloud subscription](https://www.plugnmeet.cloud).

**Note:** The plugin includes pre-configured demo credentials to help you test its features immediately. This demo server is a shared resource and is **not intended for production use** as it can be unreliable. For any important meetings, we strongly recommend using one of the options above to ensure a stable and professional experience for you and your users.

---

### Powerful Features for Your Website

#### Core Conferencing Tools
*   **HD Audio & Video:** Crystal-clear communication, screen sharing, and virtual backgrounds.
*   **Interactive Whiteboard:** Collaborate in real-time. Upload and draw on PDFs, documents, presentations, and images.
*   **Shared Notepad:** Work together on shared notes during the session.
*   **Cross-Device Compatible:** Works on all modern browsers, including Chrome, Firefox, and Safari for iOS.

#### Engagement & Collaboration
*   **Breakout Rooms:** Split participants into smaller groups for focused discussions.
*   **Polls & Voting:** Easily create polls to engage your audience and gather feedback.
*   **Raise Hand:** A simple way for participants to get your attention.
*   **Public & Private Chat:** Allow for group and one-on-one conversations with file sharing.

#### Moderator Controls & Security
*   **Advanced Lock Settings:** Fine-grained control over participant permissions (e.g., lock webcams, microphones, chat).
*   **Customizable Waiting Room:** Manage who enters your meeting and when.
*   **End-to-End Encryption (E2EE):** Secure your meetings with the highest level of privacy (on supported browsers).
*   **Secure & Encrypted:** All communication is secured using WebRTC standards.

#### Advanced Capabilities
*   **AI Meeting Agent:** Turn your meetings into actionable intelligence. Our powerful AI agent provides live spoken translations, generates automated summaries, creates full transcriptions, **and many more...**
*   **MP4 Recording:** Record your sessions to share or review later.
*   **Live Broadcasting:** Stream your meetings live to platforms like YouTube via RTMP.
*   **Stable on Any Connection:** Adaptive streaming (Simulcast & Dynacast) ensures a smooth experience, even on slower internet.
*   **Detailed Analytics:** Assess attendance and engagement with post-session reports.

== Installation ==

1.  **Set up your server:** First, ensure you have a running plugNmeet server. You can either [self-host](https://www.plugnmeet.org/docs/installation) or use the [plugNmeet cloud](https://www.plugnmeet.cloud).
2.  **Install the plugin:** Upload the plugin files to the `/wp-content/plugins/plugnmeet` directory, or install the plugin through the WordPress plugins screen directly.
3.  **Activate the plugin:** Activate the plugin through the 'Plugins' screen in WordPress.
4.  **Configure settings:** Go to the plugNmeet settings page in your WordPress admin area and enter your server URL, API Key, and Secret.
5.  **Create a room:** Go to the plugNmeet menu, create a new conference room, and use the provided shortcode to embed it on any page or post.

== Frequently Asked Questions ==

= How do I display a meeting room on a page? =

When you create or edit a room in the plugNmeet admin menu, the plugin will provide a shortcode (e.g., `[plugnmeet_room_view id="your_room_id"]`). Simply copy this shortcode and paste it into any page, post, or widget. It works with the classic editor, Gutenberg blocks, and most page builders like Elementor or Divi.

= Do I really need a separate server? =

Yes. This plugin connects your WordPress site to a plugNmeet server, which handles all the heavy lifting for the video conference (like video streaming and recording). This ensures your website's performance is not affected, even during large meetings. You can either **self-host the server for free** or use our convenient **plugNmeet cloud service**.

= What's the difference between self-hosting and the cloud? =

*   **Self-hosting** gives you maximum control, privacy, and customization. You run the open-source plugNmeet server on your own infrastructure. This is a great free option if you are comfortable with server administration.
*   **The Cloud** is the easiest way to get started. We manage the server for you, ensuring high performance and reliability without any technical setup on your part.

= How do I record a meeting? =

You can enable recording when you create a room. After the meeting ends, the recording will be processed into a simple MP4 file. You and your users can access and download these recordings from the "Recordings" tab within the meeting room interface on your WordPress page.

= Can I sell access to my webinars or meetings? =

Yes. Because plugNmeet embeds the meeting directly on your WordPress page, you can use any standard WordPress content restriction method. Simply protect the page containing the meeting shortcode with your favorite membership or e-commerce plugin (e.g., WooCommerce, MemberPress, etc.). If a user has access to the page, they have access to the meeting.

= Is it secure? =

Yes, security is a top priority.
*   All communication is encrypted by default using WebRTC standards.
*   For maximum privacy, you can enable **End-to-End Encryption (E2EE)** for your meetings.
*   Because you control the server (either self-hosted or through our private cloud), you control your data.

= Can we host the server on our own on-premises infrastructure? =

Yes, absolutely. This is one of plugNmeet's core strengths. The open-source plugNmeet server can be installed on your own on-premises hardware or in a private cloud. This means all sensitive meeting data—including client information, corporate discussions, and confidential files—never leaves your network.

This is the ideal solution for corporations, healthcare providers, government agencies, and NGOs that need to comply with strict data privacy regulations (like GDPR or HIPAA) or wish to run the service exclusively within a private corporate network.

= My users can't connect their camera. What's wrong? =

The most common reason for this is that your website is not running on **HTTPS**. Modern web browsers require a secure `https://` connection to allow access to a user's camera and microphone. Please ensure your WordPress site has a valid SSL certificate installed.

== Screenshots ==

1. The collaborative whiteboard in action during a meeting.
2. The shared notepad allows for real-time, collaborative note-taking.

== Changelog ==

For a detailed list of changes, please see the [CHANGELOG.md](https://github.com/mynaparrot/plugNmeet-WordPress/blob/main/CHANGELOG.md) file on GitHub.
