( function( $, _, masterSections ) {

	var overlayClass = oneApp.views.overlays.settings;

	oneApp.views.overlays.settings = overlayClass.extend( {
		initialize: function() {
			overlayClass.prototype.initialize.apply( this, arguments );

			if ( this.model.get( 'master' ) ) {
				this.controls.master_id.enable();
			}

			// Watch for changes on the master checkbox
			this.changeset.on( 'change:master', this.onMasterChange, this );
			// Watch for changes on the master id reference
			this.changeset.on( 'change:master_id', this.onMasterIdChange, this );
		},

		onMasterChange: function() {
			var isMaster = this.changeset.get( 'master' );

			if ( ! isMaster ) {
				// If this section isn't marked as master,
				// reset any current master_id value
				// and disable the master_id control
				this.changeset.set( 'master_id', '' );
				this.controls.master_id.disable();
			} else {
				// Enable the master_id control if
				// this section is marked as master
				this.controls.master_id.enable();
			}
		},

		onMasterIdChange: function() {
			var masterId = this.changeset.get( 'master_id' );

			if ( '' !== masterId ) {
				var master = masterSections[ masterId ];
				this.changeset.set( master );
				this.applyValues( master );
			} else {
				var originalAttributes = this.model.omit( 'id', 'master', 'master_id' );
				this.changeset.set( originalAttributes );
				this.applyValues( originalAttributes );
			}
		},

		onUpdate: function( e ) {
			overlayClass.prototype.onUpdate.apply( this, arguments );

			var masterId = this.model.get( 'master_id' );

			if ( '' !== masterId ) {
				var attributes = this.model.omit( 'id', 'master', 'master_id' );
				// Sync original master definition to current state
				masterSections[ masterId ] = attributes;

				// Sync all instances currently in page
				var instances = oneApp.builder.sections.filter( function( section ) {
					console.log( section.id );
					return section.get( 'master_id' ) === this.model.get( 'master_id' )
						&& section.id !== this.model.id;
				}, this );

				_( instances ).each( function( section ) {
					section.set( section.parse( attributes ) );
				}, this );
			}
		},
	} );

} ) ( jQuery, _, masterSections );