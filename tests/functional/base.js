define([
	'intern',
	'intern!tdd',
  '../support/builder'
], function (intern, tdd, Builder) {

	// WordPress home url, without trailing slash
	var baseUrl = intern.args.url;
	var wpUser = intern.args.user;
	var wpPass = intern.args.pass;

	// The Builder testing interface
	var builder;

	tdd.suite('One Section Per Type Layout', function () {
		tdd.before(function() {
			builder = new Builder(this.remote);
			this.remote.setWindowSize(1440, 768);

			return this.remote
				// Login
				.get(baseUrl + '/wp-admin/post-new.php?post_type=page')
				.findDisplayedByXpath('id("user_login")')
					.moveMouseTo()
					.click()
					.type(wpUser)
					.end()
				.findDisplayedByXpath('id("user_pass")')
					.moveMouseTo()
					.click()
					.type(wpPass)
					.submit();
		});

		tdd.test('Set Post title', function () {
			return builder.setPostTitle('Mixed Layout Test').sleep(1000);
		});

		tdd.test('Create Columns section', function () {
			// Create the section
			var command = builder.createSection('columns');
			command = command.sleep(1000);

			command = builder.openOverlay(command);
			command = builder.setOverlayTitle('Columns Section Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.openItemOverlay(1, command);
			command = builder.setOverlayTitle('First Column Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.setItemContent(1, 'First column content', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.openItemOverlay(2, command);
			command = builder.setOverlayTitle('Second Column Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.setItemContent(2, 'Second column content', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.openItemOverlay(3, command);
			command = builder.setOverlayTitle('Third Column Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.setItemContent(3, 'Third column content', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.moveColumn(1, 1, command);
			command = command.sleep(1000);

			command = builder.moveColumn(3, -1, command);
			command = command.sleep(1000);

			command = builder.resizeColumn(1, 1, command);
			command = command.sleep(1000);

			return command;
		});

		tdd.test('Create Banner section', function () {
			var command = builder.createSection('banner');
			command = command.sleep(1000);

			command = builder.openOverlay(command);
			command = builder.setOverlayTitle('Banner Section Title', command);
			command = command.sleep(1000);

			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.setItemContent(1, 'First slide content', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.addBannerSlide(command);
			command = command.sleep(1000);

			command = builder.setItemContent(2, 'Second slide content', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.addBannerSlide(command);
			command = command.sleep(1000);

			command = builder.setItemContent(3, 'Third slide content', command);
			command = builder.applyOverlay(command);

			return command;
		});

		tdd.test('Create Gallery section', function () {
			var command = builder.createSection('gallery');
			command = command.sleep(1000);

			command = builder.openOverlay(command);
			command = builder.setOverlayTitle('Gallery Section Title', command);
			command = command.sleep(1000);

			command = builder.applyOverlay(command);

			return command;
		});

		tdd.test('Create Panels section', function () {
			var command = builder.createSection('panels');
			command = command.sleep(1000);

			command = builder.openOverlay(command);
			command = builder.setOverlayTitle('Panels Section Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.openItemOverlay(1, command);
			command = builder.setOverlayTitle('First Item Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.setItemContent(1, 'First item content', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.addPanelsItem(command);
			command = command.sleep(1000);

			command = builder.openItemOverlay(2, command);
			command = builder.setOverlayTitle('Second Item Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.setItemContent(2, 'Second item content', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.addPanelsItem(command);
			command = command.sleep(1000);

			command = builder.openItemOverlay(3, command);
			command = builder.setOverlayTitle('Third Item Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.setItemContent(3, 'Third item content', command);
			command = builder.applyOverlay(command);

			return command;
		});

		tdd.test('Create Posts List section', function () {
			var command = builder.createSection('postlist');
			command = command.sleep(1000);

			command = builder.openOverlay(command);
			command = builder.setOverlayTitle('Posts List Section Title', command);
			command = command.sleep(1000);

			command = builder.applyOverlay(command);

			return command;
		});

		tdd.test('Publish page', function () {
			return this.remote
				// Publish page
				.findByXpath('//input[@id="publish"][1]')
					.submit();
		});
	});
});
