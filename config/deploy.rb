# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'cherbouquin'
set :repo_url, 'git@github.com:berliozd/cherbouquin.git'
set :branch, 'v1.5'
# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
# set :deploy_to, '/home/test_deploy_cap'

# Default value for :scm is :git
# set :scm, :git

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
# set :log_level, :debug

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
set :linked_files, fetch(:linked_files, []).push('application/configs/application.ini')

# Default value for linked_dirs is []
set :linked_dirs, fetch(:linked_dirs, []).push('public/CGU', 'public/images', 'public/newsletter', 'static-files', 'var/cache', 'var/log')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5

namespace :deploy do

  after :updated, :composer do
    invoke 'php:composer'
  end

  after :updated, :clean_cache do
    invoke 'php:clean_cache'
  end

end
