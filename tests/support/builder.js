define(function (require) {
		// The overlay which is currently open
	var overlaySelector = '(//div[@class="ttfmake-overlay ttfmake-configuration-overlay"])[last()]';

	function Builder(remote) {
		this.remote = remote;
  }

  Builder.prototype = {
  	// Return the slug (or type,
  	// but with exceptions) of a section
  	getSectionSlug: function(type) {
  		return 'columns' !== type && type || 'text';
  	},

  	// Sets the Post title.
  	setPostTitle: function(title, command) {
  		command = command || this.remote;
  		return command
  			.findByCssSelector('#title')
  				.moveMouseTo()
					.click()
					.type(title)
					.end();
  	},

  	// Clicks a Create Section button.
  	createSection: function(type, command) {
  		var slug = this.getSectionSlug(type);

  		command = command || this.remote;
  		return command
				// Create section
				.findByCssSelector('#ttfmake-menu-list-item-link-' + slug + ' span')
					.moveMouseTo()
					.click()
					.end();
  	},

  	// Opens the configuration overlay
  	// of the last added section.
  	openOverlay: function(command) {
  		command = command || this.remote;
  		return command
	  		// Open configuration overlay
				.findByCssSelector('.ttfmake-section:last-child .ttfmake-section-header .ttfmake-section-configure')
					.moveMouseTo()
					.click()
					.end();
  	},

  	// Clicks the cog icon of the nth(item) item.
  	openItemOverlay: function(item, command) {
  		command = command || this.remote;
  		return command
	  		// Open configuration overlay
				.findByCssSelector('.ttfmake-section:last-child  .ttfmake-section-body .ui-sortable > div:nth-child(' + item + ') .ttfmake-overlay-open')
					.moveMouseTo()
					.click()
					.end();
  	},

  	// Sets the title in the currently open
  	// configuration overlay.
  	setOverlayTitle: function(title, command) {
  		command = command || this.remote;
  		return command
  			// Set a title for the section.
  			// CSS selection is picky with overlays,
  			// using Xpath for that reason.
				.findByXpath(overlaySelector + '/div[1]/div[1]/div[2]/div[1]/input[1]')
					.moveMouseTo()
					.click()
					.type(title)
					.end();
  	},

  	// Sets the 1-indexed column element content
  	// by clicking his pencil icon
  	setItemContent: function(item, content, command) {
  		command = command || this.remote;
  		return command
  			.findByCssSelector('.ttfmake-section:last-child .ttfmake-section-body .ui-sortable > div:nth-child(' + item + ') .edit-content-link')
	  			.moveMouseTo()
					.click()
					.sleep(500)
					.end()
				.findByCssSelector('#ttfmake-tinymce-overlay iframe')
					.moveMouseTo(20, 20)
					.clickMouseButton()
					.type(content)
					.end();
  	},

  	// Drags the 1-indexed column element towards the [1|-1] direction.
  	moveColumn: function(column, direction, command) {
  		var dragSteps = 10;

  		command = command || this.remote;
  		command = command
  			.findByCssSelector('.ttfmake-section:last-child .ttfmake-section-body .ui-sortable .ttfmake-text-column:nth-child(' + column + ')')
	  			.moveMouseTo(0, 0)
	  			.pressMouseButton(0)
	  			.sleep(1000);

	  	for (var s = 0; s < dragSteps; s++) {
	  		command = command
	  			.moveMouseTo(direction * s * 10, s)
	  			.sleep(200);
	  	}

  		return command
	  			.sleep(1000)
	  			.releaseMouseButton(0)
	  			.end();
  	},

  	// Resizes the 1-based column in the given direction.
  	resizeColumn: function(column, direction, command) {
  		var dragSteps = 10;

  		command = command || this.remote;
  		return command
  			.findByCssSelector('.ttfmake-section:last-child .ttfmake-section-body .ui-sortable > div:nth-child(' + column + ')')
	  			.moveMouseTo(direction > 0 && 300 || -10, 0)
	  			.pressMouseButton(0)
	  			.sleep(1000)
	  			.moveMouseTo(direction * 100, 0)
	  			.sleep(1000)
	  			.releaseMouseButton(0)
	  			.end();
  	},

  	addBannerSlide: function(command) {
  		command = command || this.remote;
  		return command
  			.findByCssSelector('.ttfmake-section:last-child .ttfmake-banner-add-item')
	  			.moveMouseTo()
					.click()
					.end();
  	},

  	addPanelsItem: function(command) {
  		command = command || this.remote;
  		return command
  			.findByCssSelector('.ttfmake-section:last-child .ttfmp-panels-add-item')
	  			.moveMouseTo()
					.click()
					.end();
  	},

  	// Clicks the update changes button of the current overlay.
  	applyOverlay: function(command) {
  		command = command || this.remote;
  		return command
  			// Click update changes.
  			// CSS selection is picky with overlays,
  			// using Xpath for that reason.
				.findByXpath(overlaySelector + '/div[1]/div[1]/div[3]/span[1]')
					.moveMouseTo()
					.click()
					.end();
  	}
  };

  return Builder;
});