# bootstrap4 datetime picker rails

Makes [Tempus Dominus](https://github.com/tempusdominus/bootstrap-4) available to your rails appliation through the asset pipeline.

## Usage Instructions

Add the following to your `Gemfile` and then run `bundle`

`gem 'bootstrap4-datetime-picker-rails'`

Add the following to `application.js`

```
//= require moment
// If you require timezone data (see moment-timezone-rails for additional file options)
//= require moment-timezone-with-data
//= require tempusdominus-bootstrap-4
```

Add the following to `application.scss`

`@import "tempusdominus-bootstrap-4";`

Or

`*= require tempusdominus-bootstrap-4`


If you are not using Sass, add the following to your `application.css`

```
/*
...
*= require tempusdominus-bootstrap-4
...
*/
```

## Localizations

The Tempus Dominus datepicker exposes Moment.js localizations. A list of available Moment localizations are found [here](https://github.com/moment/moment/tree/master/locale).

Select which JavaScript file you want to use and then add the following line in `application.js`

```
//= require moment/<locale>.js
```

Then, where your datepicker is configured (such as a `.coffee` file), add the following configuration option:

```coffeescript
$(#datetimepicker).datetimepicker
  locale: 'de'
```

## Update Instructions
Change the `checkout_branch` variable in `Rakefile` to the version.

Change the versions in `lib/bootstrap4_datetime_picker_rails/version.rb`

Run  `bundle exec rake update`

Commit with 'Update to $VERSION release'

Run `git push`

Run `bundle exec rake build` to confirm a successful build

Run `bundle exec rake publish` to publish to rubygems (automatically creates and pushes a tag to git)

Create a release on GitHub with the new gem version
