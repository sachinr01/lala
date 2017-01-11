// simple noticon wrapper

var React = require('react');

var Dashicon = React.createClass({displayName: "Dashicon",

	propTypes: {
		name: React.PropTypes.string.isRequired,
		style: React.PropTypes.object,
		onMouseOver: React.PropTypes.func,
		onMouseOut: React.PropTypes.func
	},

	render: function() {
		return (
			React.createElement("span", {
				style: this.props.style, 
				onMouseOver: this.props.onMouseOver, 
				onMouseOut: this.props.onMouseOut, 
				className: "dashicons dashicons-"+this.props.name}, 

				this.props.children

			)
		);
	}
});

module.exports = Dashicon;