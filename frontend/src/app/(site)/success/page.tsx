import {Htag, PaymentResult, Ptag} from "@/components";

export default async function Result(){

  return (

    <div >
      <Htag tag='h2'>Результаты оплаты</Htag>
      <Ptag>
        Если вы по ошибке указали неверный адрес электронной почты, вы можете написать <a href="https://t.me/flaaim" target="_blank">мне в телеграм</a>
      </Ptag>
      <PaymentResult>

      </PaymentResult>
    </div>
  )
}
