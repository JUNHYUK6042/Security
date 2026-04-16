# User 생성과 관리

## User 조회

### 사용 명령어
```sql
  SELECT username, default_tablespace, temporary_tablespace, account_status, profile
  FROM dba_users
  ORDER BY username;
```

- User의 이름과 각 user의 여러 설정 사항을 조회한다.
- USERNAME : 사용자명
- DEFAULT_TABLESPACE : 기본으로 사용할 tablespace명
- TEMPORARY_TABLESPACE : 사용할 temporary tablespace명
- ACCOUNT_STATUS : 계정의 상태
- PROFILE : 사용 중인 profile

### DBA_USERS 구조

- 다음 명령어로 DBA_Users의 구조를 확인합니다.
