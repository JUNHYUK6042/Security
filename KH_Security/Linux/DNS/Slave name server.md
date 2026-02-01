# Slave namer server

## IXFR, AXFR, Notify

### IXFR (Incremental Zone Transfer)

- 특징
  - 빠른 동기화
  - 네트워크 트래픽 최소화
  - 마스터와 슬레이브 모두 IXFR 지원 필요

- 동작 흐름
  - Slave DNS → SOA Serial 전송
  - Master DNS → 변경된 레코드만 전송
  - Slave DNS → 변경 부분만 반영

```text
- SOA Serial을 기준으로 변경된 레코드만 전송하는 증분 전송 방식이다.
- Default 전송방법
  - Option에서 disable 할 수 있다.
  - provide-ixfr no(master), request-ixfr no(slave)
- Notify 메시지와 함께 사용됨 변경된 내용만 slave 에 전송함.
- TCP/UDP 53번 포트를 선택할 수 있음
```

### AXFR (Full Zone Transfer)

- 특징
  - 존 파일 전체 전송
  - 트래픽 사용량 큼
  - 대규모 존 환경에서는 비효율적

- 동작 흐름
  - Slave DNS → AXFR 요청
  - Master DNS → 존 전체 레코드 전송
  - Slave DNS → 전체 존 저장

```text
- DNS 존 전체를 한 번에 전송하는 Full Zone Transfer 방식이다.
- Refresh 주기에 따라 serial 값을 확인 후 zone 파일 전체를 전송함
- Refresh 주기로 인해 동기화 지연이 발생 할 수 있음
```

### Notify
- BIND-8부터 DNS Notify는 default는 on
- Serial 값이 변경되면 master는 자동으로 notify 메시지를 발생함

---

## Master Name server 설정

### named.conf 설정 파일

![06](/KH_Security/Linux/DNS/img/06.png)

- `also-notify { 192.168.35.215; };`
```text
마스터 DNS 서버에서 존(zone) 파일이 변경되었을 때
슬레이브(세컨더리) DNS 서버에게 "존이 바뀌었어!" 라고 즉시 알림(NOTIFY) 을 보내는 설정
```

### zone 파일

![07](/KH_Security/Linux/DNS/img/07.png)

- 설정 후 `systemctl restart named`를 해주어야 합니다.

---

## Slave name server

### named.conf 설정 파일

![08](/KH_Security/Linux/DNS/img/08.png)

- `masters { 192.168.35.214; };`
```text
슬레이브(Secondary) DNS 서버가 존(zone) 데이터를 받아올 마스터(Master) DNS 서버의 IP를 지정하는 설정
슬레이브는 이 IP에서만 AXFR/IXFR 요청을 수행
```

### 파일 동기화

- Slave name server에서는 `ast06.sec.zone` 파일을 직접 생성하지 않고,  
동기화를 통해 `ast06.sec.zone`파일이 자동으로 생성된 것을 확인할 수 있습니다.

![09](/KH_Security/Linux/DNS/img/09.png)  

- 해당 파일은 읽기 전용 권한으로 생성되고, 수정할 수 없습니다.

![10](/KH_Security/Linux/DNS/img/10.png)

---

## nslookup 테스트

```text
nslookup www.google.com 192.168.35.###
```

#### Master name server 테스트  
![11](/KH_Security/Linux/DNS/img/11.png)

- www.google.com의 ip 주소를 정상적으로 응답되는 것을 확인할 수 있습니다.

#### Slave name server 테스트  
![12](/KH_Security/Linux/DNS/img/12.png)

- slave name server에서도 www.google.com의 ip 주소를  
정상적으로 응답되는 것을 확인할 수 있습니다.

#### Client 테스트  

- DNS 서버가 설정되지 않은 Client에서 www.google.com IP에 대한 질의를 합니다.  
![13](/KH_Security/Linux/DNS/img/13.png)

- 다음과 같이 정상적으로 응답 받은 것을 알 수 있습니다.

---

---
