=== Alda Weather ===
Contributors:      Alda Vigdís
Tags:              block
Tested up to:      6.1
Stable tag:        0.1.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Display a local Icelandic weather report as a WordPress block

== Description ==

This plugins adds a block that displays a current local Icelandic weather report
from selected locations, using data from the Icelandic Meterological Office.

Reports are updated on an hourly basis from the apis.is service and are
reflected wherever the Alda Weather block is placed, be it in a post, page or in
your WordPress FSE layout.

The weather stations that are currently supported by the block are Reykjavík,
Hafnarfjall, Ísafjörður, Hólmavík, Akureyri, Egilsstaðaflugvöllur and Selfoss.

Note that this is a hobby project of sorts and support is not guaranteed, but
feel free to contact me if you have ideas for improvements or bug reports.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/alda-weather` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Which locations are currenty supported? =

As of version 0.1.0, the block can display weather information for Reykjavík,
Hafnarfjall, Ísafjörður, Hólmavík, Akureyri, Egilsstaðir Airport and Selfoss.

= What if my location is missing? =

Contact the author and she'd be happy to assist you. With PHP and JS/React
skills, you can also add a location of your choice and contribute it to the
codebase.

== Changelog ==

= 0.1.0 =
* Initial Release

== Known issues ==

The met office API does not provide weather descriptions for every location at
all times. As I am a very positive person when it comes to Icelandic weather
conditions, this means that the icon that gets displayed defaults on "sunny" if
no description is provided by the met office.

A live preview showing the current weather at the chosen location is not
provided in the editor, but you will get a realistic looking preview.

Always bring a warm jacket and good shoes with you wherever you go — and please
don't get lost in the wilderness!
