=== plugnmeet ===
Contributors: bobteng
Donate link: https://www.plugnmeet.org/
Tags: video conference, ai agent, webinar, virtual classroom, live streaming
Requires at least: 6.6
Tested up to: 7.1
# x-release-please-start-version
Stable tag: 2.2.9
# x-release-please-end
Requires PHP: 8.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Open-source real-time communication and multimodal AI platform for WordPress. Host live web conferences on your own site, or run autonomous 1-on-1 AI agent sessions with your visitors.

== Description ==

plugNmeet is an open-source real-time communication and multimodal AI platform for WordPress. Rooms and AI sessions load directly on your pages -- no redirect to a third-party site -- and the interface can be styled to match your brand.

You can use it in two ways:

### 1. Live Web Conference

Multi-participant HD audio/video meetings, webinars, and virtual classrooms, with an optional live AI Co-Host & Meeting Insights layer.

*   **Conferencing:** HD audio/video, screen sharing, virtual backgrounds, adaptive streaming (Simulcast & Dynacast), and cross-device support in modern browsers.
*   **Collaboration:** Interactive whiteboard (draw on PDFs, documents, presentations, and images), shared notepad, breakout rooms, polls, raise hand, and public/private chat with file sharing.
*   **Controls & security:** Lock settings, customizable waiting room, role-based permissions, WebRTC encryption, and optional End-to-End Encryption (E2EE).
*   **Reach:** MP4 recording, live streaming to platforms like YouTube via RTMP, and telephone dial-in through a SIP gateway.
*   **Optional AI Co-Host & Meeting Insights:** Live transcription, spoken translation, AI chat assistant, and automated meeting summaries. Transcripts, summaries, and AI token usage arrive as room artifacts you can review and download.

### 2. Autonomous AI Agent (1-on-1 Session)

Private 1-on-1 rooms where each visitor gets their own dedicated real-time voice & visual AI agent that can see the session and act inside it.

*   Draws on the shared whiteboard and writes in the shared notepad.
*   Launches polls and quizzes to check understanding.
*   Sees shared screens and camera feeds (vision capture).
*   Talks and listens in real time, with configurable language, voice, and speaking rate.

### Built-in WordPress AI Presets

Pick a ready-made instruction set when you create a room, or write your own:

*   **Customer Support & Product Specialist**
*   **Interactive Product Demo & Onboarding Guide**
*   **1-on-1 Tutor & Concept Explainer**
*   **Language & Communication Coach**
*   **Candidate Interview & Skill Assessment**
*   **Custom instructions** -- your own agent prompt.

### Flexible Access & AI Control

*   **Session access:** open a 1-on-1 session per visitor (with configurable concurrency limits), or restrict controlled 1-on-1 sessions to selected WordPress users.
*   **Topic boundaries:** session topic, allowed subtopics, forbidden topics, strict topic enforcement, and a topic reminder interval.
*   **Language & voice:** spoken language, reply language, instructions language, voice, and speaking rate.
*   **Session limits:** maximum session duration and maximum audio minutes.
*   **Workspace tools:** toggle whiteboard writing, whiteboard vision, notepad writing, polls, and chat; optionally allow web fetch with a curated list of knowledge URLs.
*   **Bring Your Own Key (BYOK):** connect Google Gemini or any OpenAI-compatible provider using your own API key.
*   **Model Context Protocol (MCP):** connect MCP servers to give the agent custom tools, with an allowed-tools list per server.
*   **AI Session Reports in WordPress Admin:** overall score, mastery level, narrative summary, concept badges, quiz accuracy breakdown, progress log, workspace activity, and token usage.

### How It Works

This plugin connects your WordPress site to a plugNmeet server. As a free and open-source project, you have two good options for the server:

1.  **Self-Host:** Run your own server for maximum control and privacy by following the [official installation instructions](https://www.plugnmeet.org/docs/installation).
2.  **Use the Cloud:** Get started in minutes with a ready-to-use [plugNmeet cloud subscription](https://www.plugnmeet.cloud).

**Note:** The plugin ships with pre-configured demo credentials so you can test features immediately. That demo server is a shared resource and is **not intended for production use**, since it can be unreliable. For important meetings, use one of the options above.

### WordPress Plugin Features

*   **Seamless integration:** Embed a room in any page or post with a single shortcode.
*   **Room management:** Create and manage plugNmeet rooms from the WordPress admin.
*   **Advanced analytics:** Post-meeting analytics per session, including participant engagement, talk time, and connection quality, with Excel export.
*   **Artifact management:** Access and download room artifacts such as speech transcripts, AI meeting summaries, and AI token usage/cost.
*   **Recording management:** View, download, and merge recordings from the WordPress backend.
*   **Role-based permissions:** Control who can join meetings and manage recordings based on WordPress user roles.
*   **Frontend join form:** The shortcode shows a customizable join form for name and, when required, a password.
*   **Display recordings:** Configure the shortcode to list recordings for a room, with playback and download based on the user's role.
*   **Developer friendly:** Hook into webhook events to process real-time data from your rooms.

== Installation ==

1.  **Set up your server:** First, ensure you have a running plugNmeet server. You can either [self-host](https://www.plugnmeet.org/docs/installation) or use the [plugNmeet cloud](https://www.plugnmeet.cloud).
2.  **Install the plugin:** Upload the plugin files to the `/wp-content/plugins/plugnmeet` directory, or install the plugin through the WordPress plugins screen directly.
3.  **Activate the plugin:** Activate the plugin through the 'Plugins' screen in WordPress.
4.  **Configure settings:** Go to the plugNmeet settings page in your WordPress admin area and enter your server URL, API Key, and Secret.
5.  **Create a room:** Go to the plugNmeet menu, create a new conference room, and use the provided shortcode to embed it on any page or post.

== Frequently Asked Questions ==

= What is the difference between Live Web Conference and Autonomous AI Agent (1-on-1) mode? =

**Live Web Conference** is a multi-participant room: many people join at once for a meeting, webinar, or virtual classroom, and AI is optional (transcription, spoken translation, summaries, AI chat assistant).

**Autonomous AI Agent** is a private 1-on-1 room between one visitor and a dedicated AI agent. There is no human moderator in the session, so the agent runs the conversation itself: it speaks, draws on the whiteboard, writes in the shared notepad, launches polls and quizzes, and can see shared screens or camera feeds. You control it with a preset or custom instructions, topic guardrails, language/voice settings, and session limits.

Both modes use the same rooms and shortcodes, and you can enable either one depending on what you want to offer.

= Can I use my own AI provider API key (BYOK) or connect custom MCP tools? =

Yes. In **Plug-N-Meet -> Settings** you can add your own API key for **Google Gemini** or any **OpenAI-compatible** provider, instead of relying on the server's default AI configuration.

You can also register up to three **Model Context Protocol (MCP)** servers (name, URL, headers) and choose which tools each server may expose to the agent. Note that your plugNmeet server must have the matching AI and MCP features enabled for these settings to take effect.

= How do I display a meeting room on a page? =

When you create or edit a room in the plugNmeet admin menu, the plugin will provide a shortcode (e.g., `[plugnmeet_room_view id="your_room_id"]`). Simply copy this shortcode and paste it into any page, post, or widget. It works with the classic editor, Gutenberg blocks, and most page builders like Elementor or Divi.

= How do I display a room on its own clean URL (e.g., `yoursite.com/room/room-name/`)? =

While the shortcode method is great for embedding a room inside existing content, you can also give each room its own dedicated URL. This is perfect for a clean, professional look and for sharing direct links to a meeting.

1.  **Create a "Host" Page:** First, go to your WordPress admin and create a new, empty page. You can name it "Meeting Room" or anything you like. You do not need to add any content to this page.
2.  **Select the Host Page:** Go to the **Plug-N-Meet -> Settings** page. In the "Options" section, you will find a new setting called **"Room Host Page"**. Select the page you just created from the dropdown menu and save your settings.
3.  **Save Permalinks:** Go to **Settings -> Permalinks** in your admin dashboard and simply click the "Save Changes" button. You don't need to change any settings; this just refreshes WordPress's URL rules.

That's it! Now, when you go to the **Plug-N-Meet -> Rooms** list in your admin area, the "Join" actions for each room will point to a clean URL like `yoursite.com/plugnmeet/room/your-room-id/`. When a user visits this link, the plugin will use your "Host Page" as a template, ensuring the login form appears correctly within your theme's design but without any other post content.

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

= How can I use the new webhook feature? =

The new webhook feature allows you to receive real-time notifications about events that happen in your Plug-N-Meet rooms. To use it, you can add an action to the `plugnmeet_webhook_data` hook in your own plugin or theme's `functions.php` file. For example:

`add_action( 'plugnmeet_webhook_data', 'my_plugnmeet_webhook_handler', 10, 1 );`

`function my_plugnmeet_webhook_handler(
 $data ) {`
`  // Process the webhook data here`
`}`

== Screenshots ==

1. Webcams.
2. Whiteboard.
3. Whiteboard files.
4. Shared notepad.
5. Live transcription
6. Live Chat
7. AI Chat assistant

== Changelog ==

For a detailed list of changes, please see the [CHANGELOG.md](https://github.com/mynaparrot/plugNmeet-WordPress/blob/main/CHANGELOG.md) file on GitHub.
