# Router(OSPF - Multi area).md

---

## 실습 구성도

![25](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF/25.png)

- Area 0을 중심으로 양쪽에 Area 1과 Area 2로 연결되어 있습니다.
- R0에는 가상 인터페이스를 10.10.10.1로 구축합니다.

---

## 실습 구성

- Area 0  
  - 1.1.10.1 /24  
  - 1.1.20.1 /24  
  - 1.1.30.1 /24  
  - 1.1.40.1 /24
  - 1.1.30.2 /24
  - 30.1.1.1 /24
  - 1.1.40.2 /24
  - 40.1.1.1 /24
- Area 1  
  - 1.1.1.1 /24 
  - 1.1.1.2 /24  
  - 10.1.1.2 /24
- Area 2  
  - 1.1.2.1 /24  
  - 1.1.2.2 /24  
  - 20.1.1.1 /24
- R0의 Loopback 인터페이스를 이용하여  
  Default Route를 생성하고 OSPF로 전파합니다.

---

## R0 - Network Setting

- 중심 라우터입니다.
- Loopback0 Interface를 설정 후 default-information originate를 이용하여 라우팅 정보를 전달합니다.


- R0 설정

enable  
conf t  
hostname R0  
no ip domain-lookup  

int g1/0  
ip add 1.1.10.1 255.255.255.0  
no sh  

int g2/0  
ip add 1.1.20.1 255.255.255.0  
no sh  


int g3/0  
ip add 1.1.30.1 255.255.255.0  
no sh  


int g4/0  
ip add 1.1.40.1 255.255.255.0  
no sh  

router ospf 1  
router-id 1.1.10.1  
network 1.1.10.1 0.0.0.0 area 0  
network 1.1.20.1 0.0.0.0 area 0  
network 1.1.30.1 0.0.0.0 area 0  
netwokr 1.1.40.1 0.0.0.0 area 0  

int loopback0  
ip add 10.10.10.1 255.255.255.0  

ip route 0.0.0.0 0.0.0.0 loopback0  

router ospf 1  
default-information originate  

end  
copy run start  


---

## R1 - Network Setting

- `passive-inteface` 명령어를 통해 불필요한 업데이트를 중지시킵니다.

enable  
conf t  
hostname R1  
no ip domain-lookup  

int g0/1  
ip add 10.1.1.1 255.255.255.0  
no sh  
int s0/0/0  
ip add 1.1.1.2 255.255.255.0  
no sh  

router ospf 1  
router-id 1.1.1.2  
network 1.1.1.2 0.0.0.0 area 1  
network 10.1.1.1 0.0.0.0 area 1  
passive-interface g0/1  

end  
copy run  

---

## R2 - Network Setting

- R1과 마찬가지로 불필요한 업데이트를 중지시킵니다.

enable  
conf t  
hostname R2  
no ip domain-lookup  

int g0/1  
ip add 20.1.1.1 255.255.255.0  
no sh  

int s0/0/0  
ip add 1.1.2.2 255.255.255.0  
no sh  

router ospf 1  
router-id 1.1.1.2  
network 1.1.2.2 0.0.0.0 area 2  
network 20.1.1.1 0.0.0.0 area 2  
passive-interface g0/1  

end  
copy run start  

---

## R0_1 - Network Setting

enable  
conf t  
hostname R0_1  
no ip domain-lookup  

int g0/0  
ip add 1.1.10.2 255.255.255.0  
no sh  

int s0/0/0
ip add 1.1.1.2 255.255.255.0
no sh

router ospf 1  
router-id 1.1.10.2  
network 1.1.1.1 0.0.0.0 area 1  
network 1.1.10.2 0.0.0.0 area 0  

end  
copy run start  

---

## R0_2 - Network Setting

enable  
conf t  
hostname R0_2  
no ip domain-lookup  
  
int g0/0  
ip add 1.1.20.2 255.255.255.0  
no sh  
  
int s0/0/0  
ip add 1.1.20.1 255.255.255.0  
no sh  
  
router ospf 1  
router-id 1.1.40.2  
network 1.1.20.2 0.0.0.0 area 0  
network 1.1.2.1 0.0.0.0 area 2  
  
end  
copy run start  

---

## R0_4 - Network Setting

enable  
conf t  
hostname R0_4  
no ip domain-lookup  
 
int g0/1  
ip add 40.1.11.1 255.255.255.0  
no sh  

int g0/0  
ip add 1.1.40.2 255.255.255.0  
no sh  

router ospf 1
router-id 1.1.40.2  
network 1.1.40.2 0.0.0.0 area 0  
network 40.1.1.1 0.0.0.0 area 0  
passive-interface g0/1  

end  
copy run start

---

## PC 통신 테스트

- 각각의 pc에서 가상 인터페이스를 만든 10.10.10.1로 통신 테스트를 합니다.

#### PC 1 (10.1.1.11)

![26](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF/26.png)

#### PC-2 (20.1.1.11)

![27](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF/27.png)

#### PC-3 (30.1.1.11)

![28](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF/28.png)

#### PC-4 (40.1.1.11)

![29](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF/29.png)

- 전부 다 통신이 잘 되는 것을 확인할 수 있습니다.

---

## R0 Router에서의 통신

![29](/KH_Security/Cisco%20Packet%20Tracer/img/Router%20OSPF/29.png)

- 중심 라우터에서 각 PC로 통신 테스트를 해보았습니다.  
그 결과 중심 라우터에서도 통신이 잘 되는 것을 알 수 있습니다.
