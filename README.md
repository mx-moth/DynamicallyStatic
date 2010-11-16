Dynamically static sites
========================

Add blog and article posts in to the source folder in what ever format you want,
run the generation script, and voila, you have a site with content that you can
update in a second, with the benefit of serving only static content.

Coupling with some git hooks
----------------------------

If you set up a repository of this on a remote server, and enable the git hooks
in the hooks directory, you can automatically update your site via a simple git
push command from your development machine. It does not get simpler than that!
