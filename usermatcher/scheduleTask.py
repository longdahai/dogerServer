import time
from apscheduler.schedulers.blocking import BlockingScheduler
from dataprocess.dataprepare import dataPrepare

sched = BlockingScheduler()
matcher = dataPrepare()

sched.add_job(matcher.run, 'cron', day="1-31", hour=1)
sched.start()