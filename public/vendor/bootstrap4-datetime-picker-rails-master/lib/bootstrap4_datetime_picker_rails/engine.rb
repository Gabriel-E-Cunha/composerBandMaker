require 'momentjs-rails'
require 'moment-timezone-rails'

module Bootstrap4DatetimePickerRails
  module Rails
    class Engine < ::Rails::Engine
      initializer 'bootstrap-4-datetime-picker-rails.assets.precompile' do |app|
        app.config.assets.precompile += %w[tempusdominus-bootstrap-4.js]
      end
    end
  end
end
