import vendor.pymysql_pool as pymysql_pool
from config import *
import numpy as np
import operator
import time
import json
import pandas as pd
import matplotlib.pyplot as plt
import random


class dataPrepare(object):
    def __init__(self):
        pymysql_pool.logger.setLevel('ERROR')
        config = {'host': HOST, 'user': USER, 'password': PASS, 'database': DB}
        self._pool = pymysql_pool.ConnectionPool(size=10, name="pool", **config)

    def get_all_users(self):
        get_all_user_sql = "select u.id,u.work_score,u.pretty_score,u.edu_score,b.gender,year( from_days( datediff( now( ), b.birthday))) as age,b.homeland_province,b.homeland_city,b.living_province,b.living_city,b.highestdegree,b.marital,p.gender as pgender,p.age_min,p.age_max,p.living,p.homeland,p.marital as pmarital,p.degree,h.lover_user_ids from fa_lover_user u left join fa_lover_user_basicinfo b on u.id = b.lover_user_id left join fa_lover_user_prefer p on u.id = p.lover_user_id left join fa_lover_user_match_history h on u.id = h.lover_user_id where u.status = '1'"
        conn = self._pool.get_connection()
        all_users = conn.execute_query(get_all_user_sql, dictcursor=True)
        conn.close()
        self.all_users = all_users

    """
    规整用户数据，删除错误数据；
    若用户喜好未设置，则按默认规则填充
    """

    def clean_users(self):
        users = []
        for u in self.all_users:
            score = caculate_score(u['gender'], [u["work_score"], u["pretty_score"], u["edu_score"]])
            prefer = prefer_generator(u)
            if u['lover_user_ids']:
                history = u['lover_user_ids'].strip().split(',')
                history = list(map(int, history))
            else:
                history = []
            user = {
                "id": u['id'],
                "gender": int(u['gender']),
                "score": {
                    "work": u["work_score"],
                    "pretty": u["pretty_score"],
                    "edu": u["edu_score"],
                    # 女生颜值更重要，男生工作学历分更重要
                    "total": score[0],
                    "average": score[1]
                },
                "basic": {
                    "homeland_province": u['homeland_province'],
                    "homeland_city": u['homeland_city'],
                    "living_province": u['living_province'],
                    "living_city": u['living_city'],
                    "degree": int(u['highestdegree']),
                    "marital": int(u['marital']),
                    "age": u['age']
                },
                "prefer": prefer,
                "matchhistory": history
            }
            users.append(user)
        self.all_users = users
        # print(self.all_users)
        # data = pd.DataFrame(self.all_users)
        # print(data[0:2])

    def get_match_users(self, user):
        matched_user_ids = []
        matched_users = []
        for u in self.all_users:
            if u['id'] in ([user['id']] + user['matchhistory']):
                pass
            else:
                # 判断两个用户是否匹配
                result1 = user_match(user, u)
                result2 = user_match(u, user)
                # print(result1)
                # print(result2)
                if result1 and result2:
                    matched_users.append(result1)
            # 有超过100个匹配即可停止寻找？
            if len(matched_users) >= 100:
                break
        # sort matched users order by total score,choose 1 greater 2 equal , 2 smaller
        count_greater = 0
        count_equal = 0
        count_less = 0
        sorted(matched_users, key=lambda mu: mu['score']['total'])
        # print(matched_users)
        if len(matched_users) > 1:
            uscore = u['score']['total']
            for mu in matched_users:
                if mu['score']['total'] > (uscore + 2) and count_greater == 0:
                    matched_user_ids.append(mu['id'])
                    matched_users.remove(mu)
                if mu['score']['total'] >= (uscore - 2) and mu['score']['total'] <= (uscore + 2) and count_equal < 2:
                    matched_user_ids.append(mu['id'])
                    matched_users.remove(mu)
                    count_equal += 1
                if mu['score']['total'] < (uscore - 2) and count_less < 2:
                    matched_user_ids.append(mu['id'])
                    matched_users.remove(mu)
                    count_less += 1

                if count_equal + count_less + count_greater >= 5:
                    break

            # 不足5个的时候,随机填充到5个
            if count_equal + count_less + count_greater <= 5:
                num_to_supplement = 5 - (count_equal + count_less + count_greater)
                if len(matched_users) > num_to_supplement:
                    sup_users = random.sample(matched_users, num_to_supplement)
                else:
                    sup_users = matched_users
                for su in sup_users:
                    matched_user_ids.append(su['id'])

        return matched_user_ids

    def run(self):
        self.get_all_users()
        self.clean_users()
        match_list = []
        for u in self.all_users:
            users = self.get_match_users(u)
            if len(users) > 0:
                match_list.append([u['id'], users])
        curtime = int(time.time())
        match_list = [[ml[0], ",".join('%s' % id for id in ml[1]),curtime] for ml in match_list]
        sql = "insert into fa_lover_user_match (lover_user_id,lover_user_ids,createtime) values (%s,%s,%s)"
        conn = self._pool.get_connection()
        # 保存当天匹配记录
        conn.execute_query(sql, match_list, exec_many=True)
        conn.commit()
        conn.close()


def user_match(u, tu):
    if tu['gender'] in u['prefer']['gender'] and tu['basic']['age'] in range(u['prefer']['age'][0],
                                                                             u['prefer']['age'][1]) and tu['basic'][
        'marital'] in u['prefer']['marital'] and tu['basic']['degree'] in u['prefer']['degree']:
        if len(u['prefer']['homeland_province']) == 0:
            pass
        else:
            if tu['basic']['homeland_province'] == u['basic']["homeland_province"]:
                return tu
            else:
                return False

        if len(u['prefer']['living_city']) == 0:
            if tu['basic']['living_province'] == u['basic']["living_province"]:
                return tu
            else:
                return False
        else:
            if tu['basic']['living_city'] == u['basic']["living_city"]:
                return tu
            else:
                return False
    return False


def prefer_generator(u):
    pgender = (1,) if u['gender'] == "2" else (2,)

    # 1=同城优先,2=只要同城
    if u['living']:
        if u['living'] == 1:
            living_city = (u['living_city'],)
            living_province = (u['living_province'],)
        else:
            living_city = (u['living_city'],)
            living_province = ()
    else:
        living_city = (u['living_city'],)
        living_province = (u['living_province'],)

    # 1=都可以,2=同省
    if u['homeland']:
        if u['homeland'] == 2:
            homeland_province = (u['homeland_province'],)
        else:
            homeland_province = ()
    else:
        homeland_province = ()

    # 1=未婚,2=可以离异
    # 婚姻状态:0=未婚,1=离异
    if u['pmarital']:
        if u['pmarital'] == 2:
            pmarital = (0, 1)
        else:
            pmarital = (0,)
    else:
        # 默认未婚对未婚，离异对离异
        pmarital = (int(u['marital']),)

    # age
    if u['age_min']:
        page = (u['age_min'], u['age_max'])
    else:
        if u['gender'] == 1:
            page = (u['age'] - 7, u['age'] + 3)
        else:
            page = (u['age'] - 4, u['age'] + 10)
    # degree  1=都可以,2=本科,3=硕士,4=博士
    # highestdegree  0=本科,1=硕士,2=博士
    if u['degree']:
        if u['degree'] == 1:
            pdegree = (0, 1, 2)
        elif u['degree'] == 2:
            pdegree = (1, 2)
        elif u['degree'] == 3:
            pdegree = (2,)
    else:
        pdegree = (0, 1, 2)

    prefer = {
        "gender": pgender,
        "age": page,
        "homeland_province": homeland_province,
        "living_province": living_province,
        "living_city": living_city,
        "degree": pdegree,
        "marital": pmarital
    }
    return prefer


def caculate_score(gender, scores):
    male_factor = [2, 1, 1]
    female_factor = [1, 2, 1]
    total = 0
    average = 0
    if scores[0]:
        scores = [5, 5, 5]

    if gender == "1":
        total = np.dot(male_factor, scores)
    elif gender == "2":
        total = np.dot(female_factor, scores)
    else:
        total = np.dot([1, 1, 1, ], scores)

    average = round(float(total / 3), 4)
    return (int(total), average)


if __name__ == '__main__':
    mdp = dataPrepare()
    mdp.run()
