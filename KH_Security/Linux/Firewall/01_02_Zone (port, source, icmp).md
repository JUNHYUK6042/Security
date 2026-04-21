# Zone 설정 (Port / Source / ICMP)

## Port / Source 설정

- zone에 정의되지 않은 포트나 IP는 직접 추가/삭제하여 설정합니다.
- 기본 설정으로 부족한 경우 rich rule을 사용합니다.

### 포트 / IP 설정 명령어

| 옵션 | 예시 | 설명 |
|------|------|------|
| --add-port | firewall-cmd --permanent --zone=public --add-port=80/tcp | 특정 포트 추가 |
| --remove-port | firewall-cmd --permanent --zone=public --remove-port=80/tcp | 포트 제거 |
| --add-source | firewall-cmd --permanent --zone=public --add-source=1.1.1.1/32 | 특정 IP 추가 |
| --remove-source | firewall-cmd --permanent --zone=public --remove-source=1.1.1.1/32 | IP 제거 |

---

## Port 적용

- 다음 명령어로 웹 서버(80번 포트)를 외부에서 접근 가능하도록 설정하고 적용합니다.
```
firewall-cmd --permanent --zone=public --add-port=80/tcp

fire-cmd reload
```

![12](/KH_Security/Linux/Firewall/img/12.png)

- `--permanent`로 저장된 방화벽 설정을 실제 시스템에 적용합니다.
- reload 옵션을 실행해야 설정 파일(/etc/firewalld)에 저장된 내용이 실제 방화벽 정책에 적용됩니다.

---

### Port 적용 확인

- 다음 명령어로 80번 포트를 추가한 결과를 확인합니다.
```
firewall-cmd --list-all
```

![13]()

- 현재 active zone은 public이며 ens160 인터페이스에 적용된 상태입니다.
- services 항목에 cockpit, dhcpv6-client, ssh가 허용된 상태입니다.
- ports에 80/tcp가 추가되어 웹(HTTP) 접근이 가능한 상태입니다.
- icmp-blocks가 설정되지 않아 ping 요청이 허용된 상태입니다.
- forward, masquerade, forward-ports 설정은 사용되지 않는 상태입니다.

---

## Zone 설정 - 내용 수정 및 ICMP 차단

### permanent 옵션과 reload

- `--permanent` 옵션은 설정 파일(/etc/firewalld)에 저장하는 방식입니다.
- 즉시 적용되지 않으며 `--reload`를 실행해야 실제 방화벽에 적용됩니다.
- `--permanent` 없이 실행하면 즉시 적용되지만, 영구 저장되지 않습니다.

---

### ICMP 차단

- 기본적으로 리눅스 방화벽은 ICMP(ping)를 허용합니다.
- ICMP는 type 단위로 선택적으로 차단할 수 있습니다.
- ICMP 차단은 rich rule 기반으로 동작합니다.

---

### ICMP 명령어

| 옵션 | 예시 | 설명 |
|------|------|------|
| --add-icmp-block | firewall-cmd --permanent --zone=public --add-icmp-block=echo-request | 특정 ICMP 타입 차단 |
| --remove-icmp-block | firewall-cmd --permanent --zone=public --remove-icmp-block=echo-request | ICMP 차단 해제 |
| --get-icmptypes | firewall-cmd --get-icmptypes | ICMP 타입 목록 확인 |
| --list-icmp-blocks | firewall-cmd --list-icmp-blocks | 차단된 ICMP 확인 |

---

### 실습

- 다음 명령어로 public zone에 ICMP 타입 중 echo-request(ping 요청)를 차단합니다.
````
firewall-cmd --permanent --zone=public --add-icmp-block=echo-request
```

- 다음 명령어로 변경된 설정을 적용합니다.
```
firewall-cmd --reload
```

- 다음 명령어로 icmp 차단한 것을 확인합니다.
