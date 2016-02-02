namespace :php do
    desc 'Download vendor dependencies'
    task :composer do
        on roles(:web) do
            within release_path do
                execute :composer, :install
            end
        end
    end

    desc 'Empty application cache'
    task :clean_cache do
        on roles(:web) do
            within release_path do
                execute :rm, :'-Rf', :'var/cache/*'
            end
        end
    end
end