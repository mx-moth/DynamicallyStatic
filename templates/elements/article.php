			<div class="article">

				<h2><?php echo $article['title']; ?></h2>
				<p class="details">
					<?php
					$meta = array();
					if (!empty($article['author'])) {
						$meta[] = '<span class="author">Written by ' . $article['author'] .'</span>';
					}
					if (!empty($article['date'])) {
						$meta[] = 'Published on <span class="date">' . date('Y-m-d h:ia', $article['date']) . '</span>';
					}
					if (!empty($article['link'])) {
						$meta[] = '<a href="' . $article['link'] . '" title="' . $article['title'] . '">Link to this article</a>';
					}
					if (!empty($article['tags'])) {
						$tag = $this->ds->modules['Tag'];
						$tags = array();
						foreach ($article['tags'] as $tagName) {
							$tags[] = sprintf('<a href="%s">%s</a> ', $tag->createLink($tagName), $tagName);
						}
						$meta[] = 'Tagged with ' . implode(', ', $tags);
					}
					echo implode(' | ', $meta);
					?>
				</p>
				<?php echo $article['contents']; ?>

			</div>
