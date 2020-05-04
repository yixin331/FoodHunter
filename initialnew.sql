drop table Restaurant_info CASCADE CONSTRAINTS;
drop table Restaurant_address CASCADE CONSTRAINTS;
drop table Employee CASCADE CONSTRAINTS;
drop table Manager CASCADE CONSTRAINTS;
drop table Ingredient CASCADE CONSTRAINTS;
drop table Menu CASCADE CONSTRAINTS;
drop table Dish CASCADE CONSTRAINTS;
drop table Customer CASCADE CONSTRAINTS;
drop table Customer_order CASCADE CONSTRAINTS;
drop table Employ CASCADE CONSTRAINTS;
drop table Purchase_info CASCADE CONSTRAINTS;
drop table Purchase_price CASCADE CONSTRAINTS;
drop table Made_of CASCADE CONSTRAINTS;
drop table Consist_of CASCADE CONSTRAINTS;
drop table Review CASCADE CONSTRAINTS;
drop table Design CASCADE CONSTRAINTS;

CREATE TABLE  Restaurant_info(
Name CHAR(20) PRIMARY KEY, 
Open_hours CHAR(20), 
Type CHAR(20), 
Address CHAR(50), 
Phone NUMBER(10)
);

grant select on Restaurant_info to public;

CREATE TABLE Restaurant_address (
Address CHAR(50) PRIMARY KEY, 
Postcode CHAR(7)
);

grant select on Restaurant_address to public;

CREATE TABLE Employee (
ID NUMBER(6) PRIMARY KEY, 
Phone NUMBER(10), 
Name CHAR(20),
Password NUMBER(6),
UNIQUE (Phone, Name)
);

grant select on Employee to public;

CREATE TABLE Manager (
Employee_ID NUMBER(6) PRIMARY KEY, 
FOREIGN KEY (Employee_ID) REFERENCES Employee (ID)
ON DELETE CASCADE
);

grant select on Manager to public;


CREATE TABLE Ingredient (
Name CHAR(20) PRIMARY KEY, 
Price Number(8,2)
);

grant select on Ingredient to public;

CREATE TABLE Menu (
Restaurant_name CHAR(20), 
Type CHAR(20), 
Version NUMBER(2),
PRIMARY KEY (Restaurant_name, Type, Version),
FOREIGN KEY (Restaurant_name) REFERENCES Restaurant_info (Name)
ON DELETE CASCADE
);

grant select on Menu to public;

CREATE TABLE Dish (
Name CHAR(20) PRIMARY KEY
);

grant select on Dish to public;

CREATE TABLE Customer (
Login_ID CHAR(6) PRIMARY KEY,
Name CHAR(20), 
Phone NUMBER(10), 
Address CHAR(50),
Password NUMBER(6),
UNIQUE (Name, Phone)
);

grant select on Customer to public;



CREATE TABLE Employ (
Restaurant_name CHAR(20), 
Employee_ID NUMBER(6), 
Salary NUMBER(10), 
Start_time DATE,
PRIMARY KEY (Restaurant_name, Employee_ID),
FOREIGN KEY (Restaurant_name) REFERENCES Restaurant_info (Name)
ON DELETE CASCADE,
FOREIGN KEY (Employee_ID) REFERENCES Employee (ID)
ON DELETE CASCADE
);

grant select on Employ to public;

CREATE TABLE Purchase_info (
Manager_ID NUMBER(6), 
Ingredient_name CHAR(20), 
Amount NUMBER(6,2),
FOREIGN KEY (Manager_ID) REFERENCES Manager (Employee_ID)
ON DELETE SET NULL,
FOREIGN KEY (Ingredient_name) REFERENCES Ingredient (Name)
ON DELETE CASCADE,
check(Amount>0)
);

grant select on Purchase_info to public;

CREATE TABLE Purchase_price (
Ingredient_name CHAR(20), 
Amount Number(6,2) CHECK (Amount>0), 
Total_price NUMBER(10,2),
PRIMARY KEY (Ingredient_name, Amount),
FOREIGN KEY (Ingredient_name) REFERENCES Ingredient (Name)
ON DELETE CASCADE
);

grant select on Purchase_price to public;

CREATE TABLE Made_of (
Dish_name CHAR(20), 
Ingredient_name CHAR(20),
PRIMARY KEY (Dish_name, Ingredient_name),
FOREIGN KEY (Dish_name) REFERENCES Dish (Name)
ON DELETE CASCADE,
FOREIGN KEY (Ingredient_name) REFERENCES Ingredient (Name)
ON DELETE CASCADE
);

grant select on Made_of to public;

CREATE TABLE Consist_of (
Dish_name CHAR(20), 
Menu_rest_name CHAR(20), 
Menu_version NUMBER(2), 
Menu_type CHAR(20),
Price Number(8,2),
PRIMARY KEY (Dish_name, Menu_rest_name, Menu_version, Menu_type),
FOREIGN KEY (Dish_name) REFERENCES Dish (Name)
ON DELETE CASCADE,
FOREIGN KEY (Menu_rest_name, Menu_version, Menu_type) REFERENCES Menu (Restaurant_name, Version, Type)
ON DELETE CASCADE
);

grant select on Consist_of to public;

CREATE TABLE Customer_order (
ID NUMBER(10) PRIMARY KEY, 
Dish_name CHAR(20) NOT NULL, 
Customer_ID CHAR(6) NOT NULL,
For_here_or_not CHAR(1) CHECK (For_here_or_not in ('Y','N')),
Quantity NUMBER(5),
Rest_name CHAR(20),
Menu_version NUMBER(2),
Menu_type CHAR(20),
FOREIGN KEY (Dish_name,Rest_name,Menu_version,Menu_type) REFERENCES Consist_of (Dish_name,Menu_rest_name,Menu_version,Menu_type)
ON DELETE CASCADE,
FOREIGN KEY (Customer_ID) REFERENCES Customer (Login_ID)
ON DELETE CASCADE
);

grant select on Customer_order to public;

CREATE TABLE Review (
Restaurant_name CHAR(20),
Customer_ID CHAR(6),
Rating NUMBER(1) CHECK (Rating in (1,2,3,4,5)),
FOREIGN KEY (Restaurant_name) REFERENCES Restaurant_info (Name)
ON DELETE CASCADE,
FOREIGN KEY (Customer_ID) REFERENCES Customer (Login_ID)
ON DELETE SET NULL
);

grant select on Review to public;

CREATE TABLE Design (
Manager_ID NUMBER(6), 
Menu_rest_name CHAR(20), 
Menu_type CHAR(20),  
Menu_version NUMBER(2),
Update_Time Timestamp,
FOREIGN KEY (Manager_ID) REFERENCES Manager (Employee_ID)
ON DELETE SET NULL,
FOREIGN KEY (Menu_rest_name, Menu_type, Menu_version) REFERENCES Menu (Restaurant_name, Type, Version)
ON DELETE CASCADE
);

grant select on Design to public;

CREATE ASSERTION totalEmployment
check(
    NOT EXISTS(
        (SELECT Name from Restaurant_info)
        MINUS
        (SELECT Restaurant_name from Employ)
    )
)

CREATE ASSERTION restMENU
check(
    NOT EXISTS(
        (SELECT Name from Restaurant_info)
        MINUS
        (SELECT Restaurant_name from Menu)
    )
)

CREATE ASSERTION menuContent
check(
    NOT EXISTS(
        (SELECT Name,Type,Version from Restaurant_info)
        MINUS
        (SELECT Menu_rest_name,Menu_type,Menu_version from Consist_of)
    )
)

CREATE ASSERTION dishIncluded
check(
    NOT EXISTS(
        (SELECT Name from Dish)
        MINUS
        (SELECT Dish_name from Consist_of)
    )
)

CREATE ASSERTION dishMadeOf
check(
    NOT EXISTS(
        (SELECT Name from Dish)
        MINUS
        (SELECT Dish_name from Made_of)
    )
)
CREATE ASSERTION custOrder
check(
    NOT EXISTS(
        (SELECT Login_ID from Customer)
        MINUS
        (SELECT Customer_ID from Customer_order)
    )
)
 
insert into Restaurant_info
values('Haidilao Hotpot','11:00-2:00','Chinese','5890 Number 3 Rd, Richmond, Canada',6043706665);

insert into Restaurant_info
values('Miss Korea BBQ','11:00-1:00','Korean','793 Jervis St, Vancouver, Canada',6046691225);

insert into Restaurant_info
values('Pacific Poke','10:00-19:00','Japanese','2366 Main Mall, Vancouver, Canada',6048221992);

insert into Restaurant_info
values('Cactus Club Cafe','11:00-0:00','Western','588 Burrard St, Vancouver, Canada',6046820933);

insert into Restaurant_info
values('Church Chicken','24 hours','Fast Food','3425 Main St, Vancouver, Canada',6047098541);

insert into Restaurant_address
values('5890 Number 3 Rd, Richmond, Canada','V6Y 0C2');

insert into Restaurant_address
values('793 Jervis St, Vancouver, Canada','V6E 2B1');

insert into Restaurant_address
values('2366 Main Mall, Vancouver, Canada','V6T 1Z4');

insert into Restaurant_address
values('588 Burrard St, Vancouver, Canada','V6C 1A8');

insert into Restaurant_address
values('3425 Main St, Vancouver, Canada','V5V 1E3');

insert into Employee
values(1,7784321268,'Ben',1);
insert into Employee
values(2,7782403453,'Tom',2);
insert into Employee
values(3,604890234,'Cici',3);
insert into Employee
values(4,2503420943,'Kerry',4);
insert into Employee
values(5,4440321234,'Molin',5);
insert into Employee
values(11,2509384759,'Kaili',11);
insert into Employee
values(12,8930332453,'Harry',12);
insert into Employee
values(13,9075342345,'Yixuan',13);
insert into Employee
values(14,5845734234,'Chloe',14);
insert into Employee
values(15,1234328438,'Daniel',15);

insert into Ingredient
values('Beef',6.99);
insert into Ingredient
values('Cabbage',2.98);
insert into Ingredient
values('Spaghetti',3.59);
insert into Ingredient
values('Chicken',4.98);
insert into Ingredient
values('Potato',1.99);

insert into Manager
values(11);
insert into Manager
values(12);
insert into Manager
values(13);
insert into Manager
values(14);
insert into Manager
values(15);

insert into Menu
values('Haidilao Hotpot','Main',1);
insert into Menu
values('Miss Korea BBQ','Main',1);
insert into Menu
values('Miss Korea BBQ','Snack',1);
insert into Menu
values('Pacific Poke','Main',2);
insert into Menu
values('Cactus Club Cafe','Main',3);
insert into Menu
values('Cactus Club Cafe','Side',3);
insert into Menu
values('Church Chicken','Main',1);
insert into Menu
values('Church Chicken','Snack',1);
insert into Menu
values('Church Chicken','Side',1);

insert into Dish
values('Fried chicken');
insert into Dish
values('Sliced beef');
insert into Dish
values('Mushroom spaghetti');
insert into Dish
values('Cabbage soup');
insert into Dish
values('French fries');
insert into Dish
values('Beef spaghetti');
insert into Dish
values('Borscht');
insert into Dish
values('Chicken burger');
insert into Dish
values('Mashed potato');
insert into Dish
values('Roasted chicken');
insert into Dish
values('Cabbage stirfy');
insert into Dish
values('Beef teriyaki');

insert into Customer
values('helenn','Helen',7785225532,'6335 Thunderbird Crescent, Vancouver, Canada',981119);
insert into Customer
values('oolala','Olivia',7785223402,'5728 Berton Ave, Vancouver, Canada',932402);
insert into Customer
values('jenn98','Jennie',2510934323,'5779 Birney Ave, Vancouver, Canada',989900);
insert into Customer
values('ailuvu','Alice',5748392212,'8160 Lansdowne Rd, Richmond, Canada',8928);
insert into Customer
values('8923de','Sherry',3785940234,'6081 Kathleen Ave, Burnaby, Canada',34523);



insert into Employ
values('Haidilao Hotpot',5,900,TO_DATE('2018/05/20', 'YYYY/MM/DD'));
insert into Employ
values('Miss Korea BBQ',1,900,TO_DATE('2017/09/01', 'YYYY/MM/DD'));
insert into Employ
values('Pacific Poke',2,1000,TO_DATE('2017/12/31', 'YYYY/MM/DD'));
insert into Employ
values('Cactus Club Cafe',3,1100,TO_DATE('2019/04/06', 'YYYY/MM/DD'));
insert into Employ
values('Church Chicken',4,800,TO_DATE('2018/05/31', 'YYYY/MM/DD'));
insert into Employ
values('Haidilao Hotpot',11,1100,TO_DATE('2018/05/20', 'YYYY/MM/DD'));
insert into Employ
values('Miss Korea BBQ',12,1200,TO_DATE('2018/05/31', 'YYYY/MM/DD'));
insert into Employ
values('Pacific Poke',13,1100,TO_DATE('2018/05/20', 'YYYY/MM/DD'));
insert into Employ
values('Cactus Club Cafe',14,1300,TO_DATE('2018/01/01', 'YYYY/MM/DD'));
insert into Employ
values('Church Chicken',15,950,TO_DATE('2016/02/14', 'YYYY/MM/DD'));

insert into Purchase_info
values(12,'Beef',50);
insert into Purchase_info
values(11,'Cabbage',30);
insert into Purchase_info
values(13,'Spaghetti',20);
insert into Purchase_info
values(14,'Chicken',50);
insert into Purchase_info
values(15,'Potato',40);

insert into Purchase_price
values('Beef',50,349.50);
insert into Purchase_price
values('Cabbage',30,89.40);
insert into Purchase_price
values('Spaghetti',20,71.80);
insert into Purchase_price
values('Chicken',50,249.00);
insert into Purchase_price
values('Potato',40,79.60);

insert into Made_of
values('Fried chicken','Chicken');
insert into Made_of
values('Sliced beef','Beef');
insert into Made_of
values('Mushroom spaghetti','Spaghetti');
insert into Made_of
values('Cabbage soup','Cabbage');
insert into Made_of
values('French fries','Potato');
insert into Made_of
values('Beef spaghetti','Beef');
insert into Made_of
values('Beef spaghetti','Spaghetti');
insert into Made_of
values('Borscht','Beef');
insert into Made_of
values('Borscht','Potato');
insert into Made_of
values('Borscht','Cabbage');
insert into Made_of
values('Chicken burger','Chicken');
insert into Made_of
values('Mashed potato','Potato');
insert into Made_of
values('Roasted chicken','Chicken');
insert into Made_of
values('Cabbage stirfy','Cabbage');
insert into Made_of
values('Beef teriyaki','Beef');


insert into Consist_of values('Fried chicken','Church Chicken',1,'Main',12.99);
insert into Consist_of values('Sliced beef','Haidilao Hotpot',1,'Main',9.99);
insert into Consist_of values('Cabbage stirfy','Haidilao Hotpot',1,'Main',9.99);
insert into Consist_of values('Borscht','Haidilao Hotpot',1,'Main',8.99);
insert into Consist_of values('Mushroom spaghetti','Cactus Club Cafe',3,'Main',16.99);
insert into Consist_of values('Beef spaghetti','Cactus Club Cafe',3,'Main',20.99);
insert into Consist_of values('Chicken burger','Cactus Club Cafe',3,'Main',15.99);
insert into Consist_of values('French fries','Cactus Club Cafe',3,'Side',10.99);
insert into Consist_of values('Cabbage soup','Pacific Poke',2,'Main',3.99);
insert into Consist_of values('French fries','Miss Korea BBQ',1,'Snack',9.98);
insert into Consist_of values('Cabbage soup','Miss Korea BBQ',1,'Snack',5.98);
insert into Consist_of values('Beef teriyaki','Miss Korea BBQ',1,'Main',20.99);
insert into Consist_of values('Roasted chicken','Miss Korea BBQ',1,'Main',20.99);
insert into Consist_of values('French fries','Pacific Poke',2,'Main',5.98);
insert into Consist_of values('Beef teriyaki','Pacific Poke',2,'Main',10.99);
insert into Consist_of values('Mashed potato','Church Chicken',1,'Side',4.98);
insert into Consist_of values('French fries','Church Chicken',1,'Snack',3.99);

insert into Customer_order
values(1,'Fried chicken','helenn','N',1,'Church Chicken',1,'Main');
insert into Customer_order
values(6,'Fried chicken','oolala','N',1,'Church Chicken',1,'Main');
insert into Customer_order
values(7,'Fried chicken','jenn98','N',1,'Church Chicken',1,'Main');
insert into Customer_order
values(8,'Fried chicken','ailuvu','N',1,'Church Chicken',1,'Main');
insert into Customer_order
values(9,'Fried chicken','8923de','N',1,'Church Chicken',1,'Main');
insert into Customer_order
values(2,'Sliced beef','oolala','Y',1,'Haidilao Hotpot',1,'Main');
insert into Customer_order
values(3,'Mushroom spaghetti','jenn98','Y',1,'Cactus Club Cafe',3,'Main');
insert into Customer_order
values(4,'Cabbage soup','ailuvu','Y',1,'Pacific Poke',2,'Main');
insert into Customer_order
values(5,'French fries','8923de','Y',2,'Miss Korea BBQ',1,'Snack');

insert into Review
values('Church Chicken','helenn','5');
insert into Review
values('Church Chicken','oolala','4');
insert into Review
values('Church Chicken','jenn98','3');
insert into Review
values('Church Chicken','ailuvu','4');
insert into Review
values('Church Chicken','8923de','5');
insert into Review
values('Haidilao Hotpot','oolala','2');
insert into Review
values('Cactus Club Cafe','jenn98','4');
insert into Review
values('Pacific Poke','ailuvu','3');
insert into Review
values('Miss Korea BBQ','8923de','5');

insert into Design values(11,'Haidilao Hotpot','Main',1,NULL);
insert into Design values(12,'Miss Korea BBQ','Snack',1,TO_DATE('2018/12/31', 'YYYY/MM/DD'));
insert into Design
values(13,'Pacific Poke','Main',2,TO_DATE('2019/04/06', 'YYYY/MM/DD'));
insert into Design
values(14,'Cactus Club Cafe','Main',3,TO_DATE('2018/05/20', 'YYYY/MM/DD'));
insert into Design
values(15,'Church Chicken','Main',1,TO_DATE('2016/12/31', 'YYYY/MM/DD'));

create or replace trigger addTime
before insert on Design
for each row
declare
n Timestamp;
begin
n:=SYSDATE;
:new.Update_Time:=n;
end;
/
