require_relative 'phalapi_client'

class MyFilter < PhalApi::ClientFilter
end

def show_res(response)
    puts "response: #{response.ret}, #{response.data}, #{response.msg}"
end

a_client = PhalApi::Client.create.withHost('http://demo.phalapi.net')
a_response = a_client.withService('Default.Index').withParams('username', 'dogstar').withTimeout(3000).request()

puts a_response.ret, a_response.data, a_response.msg

puts "--------------------"

a_client = PhalApi::Client.create
#a_client = PhalApi::Client.new

a_response = a_client.withHost('http://demo.phalapi.net').withService('Default.Index').withParams('username', 'dogstar').withParams('v', '1.3.0').request()

puts "--------------------"

#puts a_client
show_res a_response

puts "--------------------"

begin
    a_response = a_client.reset.withParams('one').request
rescue Exception => e  
    puts e.message  
end

puts "--------------------"

a_response = a_client.reset.withFilter(MyFilter.new).withService('Default.Index').withParams('username', 'dogstar').request
show_res a_response

puts "--------------------"

a_response = a_client.reset.withService('XXXX.noThisMethod').request
puts a_response.ret, a_response.data, a_response.msg
show_res a_response

puts 'we done!'
puts 'we done!'
puts 'we done!'
