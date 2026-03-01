# Cisco Router Redistribution

## Redistribution - RIP

### RIP : STATIC, OSPF, EIGRP

- `redistribute static` 명령은 default route 설정을 포함한다.
- Metric
  - 다른 라우팅 프로토콜로부터 이전된 경로의 metric 값은 RIP과 형식이 달라
    경로가 무시될 수 있으므로 16 이하의 적당한 값을 입력한다.
  - 전달된 경로의 metric 값이 16보다 크면 RIP는 이를 무시한다.
  - Default-metric을 지정하면 metric 입력을 생략할 수 있다.

### Command Format

```bash
redistribute static
redistribute ospf <PID> metric <값>
redistribute eigrp <AS> metric <값>
```

### Ex

```bash
redistribute static
redistribute ospf 1 metric 5
redistribute eigrp 100 metric 5
```

---

## RIP + OSPF 실습

- Interface 설정 및 라우터 기본 설정은 생략하겠습니다.
- Default Gateway는 static이기 때문에 3개의 프로토콜이 연결된 것입니다.
- classless 정보를 전송하기 위해서 사용된다

### RIP + OSPF 구성도

![16](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/16.png)

---

### RIP Setting

#### R1

```
router rip
version 2
passive-interface g0/0
no auto-summary
network 1.1.1.0
network 12.1.1.0
```

#### R2

```
router rip
version 2
passive-interface g0/0
no auto-summary
network 2.2.2.0
network 12.1.1.0
network 23.1.1.0
```

#### R3

```
router rip
version 2
no auto-summary
network 23.1.1.0
network 34.1.1.0

- Redistribute Setting -
redistribute ospf 1 metric 5
default-information originate
```

---

### OSPF Setting

#### R3

```
router ospf 1
network 23.1.1.3 0.0.0.0 area 0
network 34.1.1.3 0.0.0.0 area 0

- Redistribute Setting -
redistribute rip subnets
default-information originate
```

#### R4

```
router ospf 1
passive-interface g0/0
network 4.4.4.1 0.0.0.0 area 0
network 34.1.1.4 0.0.0.0 area 0
network 45.1.1.4 0.0.0.0 area 0
```

#### R5

```
router ospf 1
passive-interface g0/0
network 5.5.5.1 0.0.0.0 area 0
network 45.1.1.5 0.0.0.0 area 0
```

---

### PC에서의 통신 테스트

- PC1(1.1.1.11)  
![17](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/17.png)

- PC2(2.2.2.11)  
![18](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/18.png)

- PC3(4.4.4.11)  
![19](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/19.png)

- PC4(5.5.5.11)  
![20](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/20.png)

---

## RIP + EIGRP 실습

### Command Format

```bash
redistribute static
redistribute rip metric <Bandwidth> <Delay> <Reliability> <Load> <MTU>
redistribute ospf <Process-ID> metric <Bandwidth> <Delay> <Reliability> <Load> <MTU>
```

### Ex

```bash
redistribute static
redistribute rip metric 10000 10 255 1 1500
redistribute ospf 1 metric 10000 1000 255 1 1500
```

- redistribute static 명령은 default route 설정을 포함한다

### RIP + EIGRP 구성도

![21](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/21.png)

---

### RIP Setting

#### R1

```
router rip
verison 2
passive-interface g0/0
no auto-summary
network 1.1.1.0
network 12.1.1.0
```

#### R2

```
router rip
version 2
passive-interface g0/0
no auto-summary
network 2.2.2.0
network 12.1.1.0
network 23.1.1.0
```

#### R3

```
router rip
version 2
no auto-summary
network 23.1.1.0
network 34.1.1.0

- Redistribute Setting -
redistribute eigrp 100 metric 5
default-information originate
```

---

### EIGRP Setting

#### R3

```
router eigrp 100
no auto-summary
network 23.1.1.3 0.0.0.0
network 34.1.1.0 0.0.0.0

- Redistribute Setting -
redistribute static
redistribuet rip 10000 10 255 1 1500
default-information originate
```

#### R4

```
router eigrp 100
passive-interface g0/0
no auto-summary
network 4.4.4.1 0.0.0.0
network 34.1.1.4 0.0.0.0
network 45.1.1.4 0.0.0.0
```

#### R5

```
router eigrp 100
passive-interface g0/0
no auto-summary
network 5.5.5.1 0.0.0.0
network 45.1.1.5 0.0.0.0
```

---

### PC에서의 통신 테스트

- 위에 `RIP + OSPF`와 같이 통신이 잘 되는 것을 알 수 있습니다.

- PC1(1.1.1.11)  
![22](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/22.png)

- PC2(2.2.2.11)  
![23](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/23.png)

- PC3(4.4.4.11)  
![24](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/24.png)

- PC4(5.5.5.11)  
![25](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20Redistribute/25.png)

---
