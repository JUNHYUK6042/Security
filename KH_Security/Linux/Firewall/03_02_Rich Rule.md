# Rich Rule 설정 ( Log, TimeOut)

## Log 설정

- log는 방화벽에서 허용되거나 차단된 접근 기록을 남길 때 사용합니다.
- 어떤 IP가 어떤 서비스에 접근했는지 확인할 수 있어 보안 분석에 중요합니다.
- 공격 시도나 비정상 접근을 추적할 때 자주 사용합니다.

### 문법
```
log prefix="접두어" level="level" limit value="횟수/시간"
```
- 단위 : s(초), m(분), h(시간), d(day)
- prefix : 로그를 구분하기 위한 이름
- level : 로그 중요도
- limit : 일정 시간 동안 기록 횟수 제한

### 예시

log prefix="echo drop"

- ping 차단 로그를 echo drop이라는 이름으로 기록합니다.

### log level

- emerg : 전체 시스템 경고
- alert, crit, error, warning, notice, info : 일반 로그 기록
- debug : 디버깅용 로그 (에러 발생시에만 로그 남김)

### log 파일 위치

- /var/log/messages
- /var/log/firewalld

---

## Action 설정

- action은 rule에 대해 실제 어떤 동작을 할지 결정합니다.

### 종류

- accept : 연결 허용
- drop : 조용히 차단 (응답 없음)
- reject : 차단 후 응답 전송
- mark : 패킷에 표시 설정

---

## Rich Rule 적용 예시

### ping 차단 + 로그 기록

- 다음 명령어로 ICMP(ping)를 차단하면서 로그를 남깁니다.
```
firewall-cmd --permanent --zone=public --add-rich-rule='rule protocol value="icmp" log prefix="echo drop" drop'
```

- ping 요청은 차단되고 로그는 기록됩니다.

### 설정 적용

- 다음 명령어로 저장된 설정을 실제 방화벽에 적용합니다.
```
firewall-cmd --reload
```

---

### 설정 확인

- 다음 명령어로 적용된 rule을 확인합니다.
```
firewall-cmd --list-all
```

![30](/KH_Security/Linux/Firewall/img/30.png)

- 출력 결과에서 다음 rule을 확인할 수 있습니다.
- 즉, ICMP 요청은 차단되고 로그가 남는 상태입니다.

---

### 로그 확인

- Windows환경에서 192.168.12.11로 Ping을 보냅니다.

![31](/KH_Security/Linux/Firewall/img/31.png)

- 그 다음 명령어로 실제 로그를 확인합니다.
```
journalctl -k | grep "echo drop"
```

![32](/KH_Security/Linux/Firewall/img/32.png)

- ping을 보내면 요청 시간 초과가 발생하고,
- journalctl에서 차단 로그가 기록된 것을 확인할 수 있습니다.

---

## Timeout 설정

### Timeout 개념

- timeout은 일정 시간 동안만 임시로 rule을 적용할 때 사용합니다.
- 테스트나 일시적인 접근 허용에 사용합니다.

### 특징

- --timeout은 --permanent와 함께 사용할 수 없습니다.
- 영구 저장이 아닌 일시적 설정입니다.
- 남은 시간은 확인할 수 없습니다.
- 현재 적용 여부는 --list-all로 확인합니다.

### 단위

- s : 초
- m : 분
- h : 시간
- d : 일

### 예시

- 다음 명령어는 http 서비스를 1시간 동안만 허용합니다.
```
firewall-cmd --zone=public --add-service=http --timeout=1h
```

![33](/KH_Security/Linux/Firewall/img/33.png)

- http 서비스를 1시간 동안만 허용하는 명령어 입니다.

---

## 요약 정리

- log는 방화벽에서 허용되거나 차단된 접근 기록을 남길 때 사용합니다.
- 공격 시도나 비정상 접근을 추적하고 분석할 때 중요합니다.
- action은 트래픽을 허용(accept), 차단(drop/reject), 표시(mark)하는 동작을 결정합니다.
- drop은 응답 없이 차단하고, reject는 응답을 보내며 차단합니다.
- ping 차단과 동시에 log를 설정하면 차단 기록을 journalctl로 확인할 수 있습니다.
- timeout은 일정 시간 동안만 임시로 설정을 적용할 때 사용합니다.
- `--timeout`은 `--permanent`와 함께 사용할 수 없으며 일시적인 설정만 가능합니다
