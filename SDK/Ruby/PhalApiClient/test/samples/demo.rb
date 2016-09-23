# -*- encoding: utf-8 -*-

require 'rubygem'
require 'phalapi'

class MyFilter < PhalApi::Client::Filter
end

def break_case
    puts "---------------------------------------------------"
    puts "\n"
end

puts "Here we go ..."
puts "Here we go ... ..."
puts "Here we go !!!\n\n"

puts "#case 1: 正常请求\n\n"
a_client = PhalApi::Client.create.withHost('http://demo.phalapi.net')
a_response = a_client.withService('Default.Index').withParams('username', 'dogstar').withTimeout(3000).request()

puts a_client.to_s + "\n" + a_response.to_s
break_case

puts "#case 2: 重置再请求\n\n"
a_response = a_client.reset.withService('Default.Index').withParams('username', 'Nio').request()

puts a_client.to_s + "\n" + a_response.to_s
break_case

puts "#case 3: 请求不存在的接口\n\n"
a_response = a_client.reset.withService('XXXX.noThisMethod').request

puts a_client.to_s + "\n" + a_response.to_s
break_case

puts "#case 4: 请求时缺少必要参数\n\n"
begin
    a_response = a_client.reset.withParams('one').request
rescue Exception => e  
    puts a_client.to_s
    puts ""
    puts "Exception: " + e.message  
end
break_case

puts "#case 5: 自定义过滤器\n\n"
a_response = a_client.reset.withFilter(MyFilter.new).withService('Default.Index').withParams('username', 'dogstar').request

puts a_client.to_s + "\n" + a_response.to_s
break_case

puts 'We done!'
puts 'We done!'
puts 'We done!'
