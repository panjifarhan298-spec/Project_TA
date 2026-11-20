const menuBtn=document.getElementById('menuBtn');
const sideMenu=document.getElementById('sideMenu');
if(menuBtn) menuBtn.addEventListener('click',()=>sideMenu.classList.toggle('open'));

function drawLine(canvasId, points){
 const c=document.getElementById(canvasId); if(!c)return;
 const ctx=c.getContext('2d'), dpr=window.devicePixelRatio||1, w=c.clientWidth, h=c.clientHeight;
 c.width=w*dpr;c.height=h*dpr;ctx.scale(dpr,dpr);
 ctx.clearRect(0,0,w,h);
 ctx.strokeStyle='#fff';ctx.lineWidth=1;ctx.globalAlpha=.45;
 for(let y=15;y<h;y+=22){ctx.beginPath();ctx.moveTo(0,y);ctx.lineTo(w,y);ctx.stroke();}
 ctx.globalAlpha=1;ctx.strokeStyle='#149cc5';ctx.lineWidth=2;ctx.beginPath();
 points.forEach((p,i)=>{let x=10+i*(w-20)/(points.length-1),y=h-10-p*(h-25);i?ctx.lineTo(x,y):ctx.moveTo(x,y)});ctx.stroke();
}
function drawBar(id, vals){
 const c=document.getElementById(id);if(!c)return;const ctx=c.getContext('2d'),w=c.clientWidth,h=c.clientHeight;
 ctx.clearRect(0,0,w,h);let bw=w/vals.length*.45;
 vals.forEach((v,i)=>{let x=18+i*w/vals.length,y=h-10-v*(h-25);ctx.fillStyle=i%3===0?'#20dd25':i%3===1?'#f0d020':'#f34b4b';ctx.fillRect(x,y,bw,h-10-y)});
}
function drawPie(id){
 const c=document.getElementById(id);if(!c)return;const ctx=c.getContext('2d'),w=c.clientWidth,h=c.clientHeight,r=Math.min(w,h)*.32,x=w/2,y=h/2;
 let vals=[32,3,5], colors=['#19df22','#ffb01f','#ef4e4e'],a=-Math.PI/2,total=40;
 vals.forEach((v,i)=>{let b=a+v/total*Math.PI*2;ctx.beginPath();ctx.moveTo(x,y);ctx.arc(x,y,r,a,b);ctx.closePath();ctx.fillStyle=colors[i];ctx.fill();a=b});
}
drawBar('barChart',[.35,.5,.62,.72,.78,.86]);
drawLine('lineChart',[.22,.32,.42,.51,.61,.74,.86]);
drawPie('pieChart');
drawLine('lineChartBig',[.2,.28,.36,.45,.55,.63,.7,.78,.84]);
drawLine('lineChartBig2',[.15,.22,.3,.4,.48,.59,.68,.75,.82]);
window.addEventListener('resize',()=>{drawBar('barChart',[.35,.5,.62,.72,.78,.86]);drawLine('lineChart',[.22,.32,.42,.51,.61,.74,.86]);drawPie('pieChart');});
