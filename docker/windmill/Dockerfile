FROM microsoft/dotnet:2.1-sdk as build

ADD . /app
WORKDIR /app
RUN dotnet restore --no-cache && dotnet publish -o ./build -c Release && rm -Rf /root/.nuget

# microsoft/dotnet:2.1-runtime-alpine is for some reason very slow - until this is resolved we need to use the large default runtime image.
FROM microsoft/dotnet:2.1-runtime

COPY --from=build /app/build /app
WORKDIR /app

# Here will be a command that runs the engine as a background service and receives incoming analysis requests
# e.g. CMD dotnet Windmill.dll analyse --port=1234