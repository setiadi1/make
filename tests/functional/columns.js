define([
	'intern',
	'intern!tdd',
  '../support/builder',
  '../support/content',
], function (intern, tdd, Builder, content) {

	// WordPress home url, without trailing slash
	var baseUrl = intern.args.url;
	var wpUser = intern.args.user;
	var wpPass = intern.args.pass;

	// The Builder testing interface
	var builder;

	tdd.suite('Columns feature test', function () {
		tdd.before(function() {
			builder = new Builder(this.remote);
			this.remote.setWindowSize(1440, 768);

			return this.remote.get(baseUrl + '/wp-admin/post-new.php?post_type=page');
		});

		tdd.test('Set Post title', function () {
			var command = builder.showCursor();
			command = builder.setPostTitle('Columns feature test', command);
			command = command.sleep(1000);

			return command;
		});

		tdd.test('Create Columns section', function () {
			var command = builder.showCursor();

			// Create the section
			command = builder.createSection('columns', command);
			command = command.sleep(1000);

			return command;
		});

		tdd.test('Fill columns content', function () {
			var command = builder.showCursor();

			command = builder.openOverlay(command);
			command = builder.setOverlayTitle('Columns Section Title', command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.openItemDropdown(1, command);
			command = builder.openItemEditor(1, command);
			command = builder.setContent(content.lorem.short, command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.openItemDropdown(2, command);
			command = builder.openItemEditor(2, command);
			command = builder.setContent(content.lorem.medium, command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			command = builder.openItemDropdown(3, command);
			command = builder.openItemEditor(3, command);
			command = builder.setContent(content.lorem.long, command);
			command = builder.applyOverlay(command);
			command = command.sleep(1000);

			return command;
		});

		tdd.test('Move and resize columns', function () {
			var command = builder.showCursor();

			command = builder.moveColumn(1, 1, command);
			command = command.sleep(1000);

			command = builder.moveColumn(3, -1, command);
			command = command.sleep(1000);

			command = builder.resizeColumn(1, 1, command);
			command = command.sleep(1000);

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
