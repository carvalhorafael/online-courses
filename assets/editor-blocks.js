( function ( blocks, blockEditor, components, element, i18n ) {
	const el = element.createElement;
	const __ = i18n.__;
	const TextControl = components.TextControl;
	const TextareaControl = components.TextareaControl;

	function stringsToText( items ) {
		return Array.isArray( items ) ? items.join( "\n" ) : "";
	}

	function textToStrings( value ) {
		return String( value || "" )
			.split( "\n" )
			.map( function ( item ) {
				return item.trim();
			} )
			.filter( Boolean );
	}

	function sectionsToText( sections ) {
		if ( ! Array.isArray( sections ) ) {
			return "";
		}

		return sections
			.map( function ( section ) {
				const lines = [
					"# " + [ section.title || "", section.meta || "" ].join( " | " ).trim(),
				];
				const lessons = Array.isArray( section.lessons ) ? section.lessons : [];

				lessons.forEach( function ( lesson ) {
					lines.push(
						"- " +
							[
								lesson.title || "",
								lesson.duration || "",
								lesson.preview ? "preview" : "",
							]
								.join( " | " )
								.trim()
					);
				} );

				return lines.join( "\n" );
			} )
			.join( "\n\n" );
	}

	function textToSections( value ) {
		const sections = [];
		let currentSection = null;

		String( value || "" )
			.split( "\n" )
			.forEach( function ( rawLine ) {
				const line = rawLine.trim();

				if ( ! line ) {
					return;
				}

				if ( line.indexOf( "# " ) === 0 ) {
					const parts = line
						.slice( 2 )
						.split( "|" )
						.map( function ( part ) {
							return part.trim();
						} );

					currentSection = {
						title: parts[ 0 ] || "",
						meta: parts[ 1 ] || "",
						lessons: [],
					};
					sections.push( currentSection );
					return;
				}

				if ( line.indexOf( "- " ) === 0 && currentSection ) {
					const parts = line
						.slice( 2 )
						.split( "|" )
						.map( function ( part ) {
							return part.trim();
						} );

					currentSection.lessons.push( {
						title: parts[ 0 ] || "",
						duration: parts[ 1 ] || "",
						preview: ( parts[ 2 ] || "" ).toLowerCase() === "preview",
					} );
				}
			} );

		return sections;
	}

	function groupsToText( groups ) {
		if ( ! Array.isArray( groups ) ) {
			return "";
		}

		return groups
			.map( function ( group ) {
				return [ group.label || "", group.title || "", group.description || "" ].join( " | " );
			} )
			.join( "\n" );
	}

	function textToGroups( value ) {
		return String( value || "" )
			.split( "\n" )
			.map( function ( line ) {
				const parts = line.split( "|" ).map( function ( part ) {
					return part.trim();
				} );

				return {
					label: parts[ 0 ] || "",
					title: parts[ 1 ] || "",
					description: parts[ 2 ] || "",
				};
			} )
			.filter( function ( group ) {
				return group.label || group.title || group.description;
			} );
	}

	function editorShell( title, children ) {
		return el(
			"div",
			{
				className: "online-courses-editor-block",
				style: {
					border: "1px solid #dcdcde",
					borderRadius: "4px",
					padding: "16px",
				},
			},
			el( "p", { style: { marginTop: 0, fontWeight: 700 } }, title ),
			children
		);
	}

	function textFields( props, fields ) {
		return fields.map( function ( field ) {
			return el( TextControl, {
				key: field.key,
				label: field.label,
				value: props.attributes[ field.key ] || "",
				onChange: function ( value ) {
					const next = {};
					next[ field.key ] = value;
					props.setAttributes( next );
				},
			} );
		} );
	}

	blocks.registerBlockType( "online-courses/learning-outcomes", {
		title: __( "Course learning outcomes", "online-courses" ),
		icon: "welcome-learn-more",
		category: "widgets",
		attributes: {
			eyebrow: { type: "string", default: __( "Learning outcomes", "online-courses" ) },
			title: { type: "string", default: __( "What you will be able to apply.", "online-courses" ) },
			description: {
				type: "string",
				default: __( "Describe the practical results students should get from this course.", "online-courses" ),
			},
			items: {
				type: "array",
				default: [
					__( "Clarify the first practical result students will be able to apply.", "online-courses" ),
					__( "Describe the second result students should achieve.", "online-courses" ),
					__( "Show the third outcome that makes the course valuable.", "online-courses" ),
				],
			},
		},
		edit: function ( props ) {
			return editorShell(
				__( "Learning outcomes", "online-courses" ),
				el(
					"div",
					null,
					textFields( props, [
						{ key: "eyebrow", label: __( "Eyebrow", "online-courses" ) },
						{ key: "title", label: __( "Title", "online-courses" ) },
						{ key: "description", label: __( "Description", "online-courses" ) },
					] ),
					el( TextareaControl, {
						label: __( "Outcomes, one per line", "online-courses" ),
						value: stringsToText( props.attributes.items ),
						onChange: function ( value ) {
							props.setAttributes( { items: textToStrings( value ) } );
						},
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );

	blocks.registerBlockType( "online-courses/course-curriculum", {
		title: __( "Course curriculum", "online-courses" ),
		icon: "welcome-write-blog",
		category: "widgets",
		attributes: {
			eyebrow: { type: "string", default: __( "Course curriculum", "online-courses" ) },
			title: { type: "string", default: __( "Program structure", "online-courses" ) },
			description: { type: "string", default: __( "Organize the modules and lessons students will follow.", "online-courses" ) },
			sections: {
				type: "array",
				default: [
					{
						title: __( "Module 1", "online-courses" ),
						meta: __( "3 lessons", "online-courses" ),
						lessons: [
							{
								title: __( "Introduce the context and the main problem", "online-courses" ),
								duration: __( "5 min", "online-courses" ),
								preview: true,
							},
						],
					},
				],
			},
		},
		edit: function ( props ) {
			return editorShell(
				__( "Course curriculum", "online-courses" ),
				el(
					"div",
					null,
					textFields( props, [
						{ key: "eyebrow", label: __( "Eyebrow", "online-courses" ) },
						{ key: "title", label: __( "Title", "online-courses" ) },
						{ key: "description", label: __( "Description", "online-courses" ) },
					] ),
					el( TextareaControl, {
						label: __( "Curriculum", "online-courses" ),
						help: __( "Use # Module title | meta, then - Lesson title | duration | preview.", "online-courses" ),
						value: sectionsToText( props.attributes.sections ),
						rows: 8,
						onChange: function ( value ) {
							props.setAttributes( { sections: textToSections( value ) } );
						},
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );

	blocks.registerBlockType( "online-courses/requirements", {
		title: __( "Course requirements", "online-courses" ),
		icon: "yes-alt",
		category: "widgets",
		attributes: {
			eyebrow: { type: "string", default: __( "Requirements", "online-courses" ) },
			title: { type: "string", default: __( "Before you begin", "online-courses" ) },
			description: { type: "string", default: __( "Clarify the minimum context students should have before starting.", "online-courses" ) },
			items: {
				type: "array",
				default: [
					__( "List the minimum knowledge, tools or context needed before starting.", "online-courses" ),
				],
			},
		},
		edit: function ( props ) {
			return editorShell(
				__( "Requirements", "online-courses" ),
				el(
					"div",
					null,
					textFields( props, [
						{ key: "eyebrow", label: __( "Eyebrow", "online-courses" ) },
						{ key: "title", label: __( "Title", "online-courses" ) },
						{ key: "description", label: __( "Description", "online-courses" ) },
					] ),
					el( TextareaControl, {
						label: __( "Requirements, one per line", "online-courses" ),
						value: stringsToText( props.attributes.items ),
						onChange: function ( value ) {
							props.setAttributes( { items: textToStrings( value ) } );
						},
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );

	blocks.registerBlockType( "online-courses/audience-fit", {
		title: __( "Course audience", "online-courses" ),
		icon: "groups",
		category: "widgets",
		attributes: {
			eyebrow: { type: "string", default: __( "Audience fit", "online-courses" ) },
			title: { type: "string", default: __( "Who this course is for.", "online-courses" ) },
			description: { type: "string", default: __( "Describe the profiles that will benefit most from this course.", "online-courses" ) },
			groups: {
				type: "array",
				default: [
					{
						label: __( "Primary audience", "online-courses" ),
						title: __( "Professionals who need this operating skill", "online-courses" ),
						description: __( "Explain why this profile should take the course.", "online-courses" ),
					},
				],
			},
		},
		edit: function ( props ) {
			return editorShell(
				__( "Audience", "online-courses" ),
				el(
					"div",
					null,
					textFields( props, [
						{ key: "eyebrow", label: __( "Eyebrow", "online-courses" ) },
						{ key: "title", label: __( "Title", "online-courses" ) },
						{ key: "description", label: __( "Description", "online-courses" ) },
					] ),
					el( TextareaControl, {
						label: __( "Audience groups", "online-courses" ),
						help: __( "One per line: label | title | description.", "online-courses" ),
						value: groupsToText( props.attributes.groups ),
						rows: 6,
						onChange: function ( value ) {
							props.setAttributes( { groups: textToGroups( value ) } );
						},
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );

	blocks.registerBlockType( "online-courses/instructor-bio", {
		title: __( "Course instructor", "online-courses" ),
		icon: "businessperson",
		category: "widgets",
		attributes: {
			eyebrow: { type: "string", default: __( "Instructor", "online-courses" ) },
			name: { type: "string", default: "" },
			role: { type: "string", default: __( "Course instructor", "online-courses" ) },
			bio: { type: "string", default: __( "Add a short instructor bio connected to the course topic.", "online-courses" ) },
			imageUrl: { type: "string", default: "" },
			highlights: {
				type: "array",
				default: [ __( "Connects course concepts to practical routines.", "online-courses" ) ],
			},
		},
		edit: function ( props ) {
			return editorShell(
				__( "Instructor", "online-courses" ),
				el(
					"div",
					null,
					textFields( props, [
						{ key: "eyebrow", label: __( "Eyebrow", "online-courses" ) },
						{ key: "name", label: __( "Name", "online-courses" ) },
						{ key: "role", label: __( "Role", "online-courses" ) },
						{ key: "imageUrl", label: __( "Image URL", "online-courses" ) },
					] ),
					el( TextareaControl, {
						label: __( "Bio", "online-courses" ),
						value: props.attributes.bio || "",
						onChange: function ( value ) {
							props.setAttributes( { bio: value } );
						},
					} ),
					el( TextareaControl, {
						label: __( "Highlights, one per line", "online-courses" ),
						value: stringsToText( props.attributes.highlights ),
						onChange: function ( value ) {
							props.setAttributes( { highlights: textToStrings( value ) } );
						},
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
