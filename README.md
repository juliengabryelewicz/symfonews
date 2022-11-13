# Symfonews

Symfonews is a Symfony app which saves all News from RSS feeds you want to use.

## Commands available

feeds:read : Retrieve all news from active feeds. If a news is already available in the database, it won't be saved again.

feeds:delete : Delete news older than a specific number of days. You can specify it in services.yaml with the "nb_days" parameter. 

you can use these commands as cron tasks.