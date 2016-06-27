<h3><?php _e('How to rate our plugin', $this->text_domain); ?></h3>
<p><?php _e('We work very hard to bring to life the best solutions to improve your WordPress experience, so, if you like what you see, let us know by rating our plugin on CodeCanyon.', $this->text_domain); ?></p>
<p><?php _e('Your rating also helps us to understand what we need to improve and what do you think should be the next step of our development process.', $this->text_domain); ?></p>
<p><?php printf(__('To rate, go to your <a href="%s">CodeCanyon Dashboard</a>, and click on the Downloads tab.', $this->text_domain), 'http://codecanyon.net/author_dashboard'); ?></p>
<p><?php printf(__('Search for the plugin <strong>%s</strong> and select the number of stars you want to give.', $this->text_domain), $this->get_plugin_info('Name')); ?></p>
<p><img src="<?php echo $this->get_plugin_url(); ?>assets/img/rate-our-plugin.png" alt="Rate our plugin"></p>
<p><?php _e('Thank you.', $this->text_domain); ?></p>